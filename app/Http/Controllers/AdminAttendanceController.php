<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Exports\UsersExport;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\CounselorRequest;
use App\Http\Requests\ResidenceRequest;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Rest;
use App\Models\ScheduleType;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\Admin;
use App\Models\AdminComment;
use App\Models\AdminDetail;
use App\Models\AttendanceType;
use App\Models\Counselor;
use App\Models\DisabilityCategory;
use App\Models\Role;
use App\Models\Residence;
use App\Models\SpecialSchedule;
use App\Models\UserDetail;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;


use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminAttendanceController extends Controller
{

    public function showTimecard($yearmonth = null, $user_id = null)
    {
        $adminId = Auth::id();
        $admin = Admin::with('adminDetail')->find($adminId);
        $companyId = $admin->adminDetail->company_id;
        $users = User::whereHas('userDetail', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with('userDetail')->get();

        if ($yearmonth == null) {
            $today = Carbon::today();
            $year = $today->year;
            $month = sprintf("%02d", $today->month);
        } else {
            $yearMonthArr = explode("-", $yearmonth);
            $year = $yearMonthArr[0];
            $month = sprintf("%02d", $yearMonthArr[1]);
        }


        if ($user_id == null) {
            $userDetail = UserDetail::where('company_id', $companyId)->first();
            $user_id = $userDetail->user_id;
        }

        // 表示データの作成
        $monthlyAttendanceData = [];
        $thisMonthWorkSchedules = WorkSchedule::with(['specialSchedule.schedule_type', 'scheduleType', 'attendances' => function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        }, 'attendances.rests', 'attendances.overtimes', 'attendances.adminComments.admin'])->whereYear('date', $year)->whereMonth('date', $month)->orderBy('date', 'asc')->get(); // dd($thisMonthWorkSchedules);
        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curAttendance = $workSchedule->attendances->first();

            if (!$curAttendance) {
                $curAttendanceObj = [
                    'attendance_id' => "",
                    'date' => $workSchedule->date,
                    'date' => $workSchedule->date,
                    'scheduleType' => $workSchedule->specialSchedule == null ? $workSchedule->scheduleType->name : $workSchedule->specialSchedule->schedule_type->name,
                    'bodyTemp' => "",
                    'checkin' => "",
                    'checkout' => "",
                    'is_overtime' => "",
                    'rest' => "",
                    'overtime' => "",
                    'duration' => "",
                    'workDescription' => "",
                    'workComment' => "",
                    'admin_comment' => "",
                ];
                array_push($monthlyAttendanceData, $curAttendanceObj);
            } else {
                $curRests = $curAttendance->rests;
                //休憩は複数回入る可能性あり。
                $restTimes = [];
                foreach ($curRests as $rest) {
                    $restTimes[] = Carbon::parse($rest->start_time)->format('H:i') . '-' . Carbon::parse($rest->end_time)->format('H:i');
                }
                $restTimeString = implode("<br>", $restTimes);

                $curOvertime = $curAttendance->overtimes->first();

                //ここから1日の勤務時間の計算 1. 出勤 10時以前→10時、10時以降→15分単位で切り上げ
                $checkInTimeForCalc = Carbon::parse($curAttendance->check_in_time);
                $checkOutTimeForCalc = Carbon::parse($curAttendance->check_out_time);
                $baseTimeForIn = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 10:00:00');
                $baseTimeForOut = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 15:00:00');

                $isOvertime = $curAttendance->is_overtime;

                //出勤時間の切り上げ
                if ($checkInTimeForCalc->lt($baseTimeForIn)) {
                    $checkInTimeForCalc->hour(10)->minute(0)->second(0);
                } else {
                    $checkInTimeForCalc->ceilMinute(15);
                }


                //退勤時間の切り下げ 残業なし(isOvertime=0) かつ 15時以降の打刻であれば
                if ($checkOutTimeForCalc->gt($baseTimeForOut) && $isOvertime == 0) {
                    $checkOutTimeForCalc->hour(15)->minute(0)->second(0);
                } else {
                    $checkOutTimeForCalc->floorminute(15);
                }

                $totalRestDuration = CarbonInterval::seconds(0); // 0秒で初期化

                foreach ($curRests as $rest) {
                    $restStart = Carbon::parse($rest->start_time);
                    $restEnd = Carbon::parse($rest->end_time);
                    $restDuration = $restStart->floorminute(15)->diff($restEnd->ceilminute(15));

                    $totalRestDuration = $totalRestDuration->add($restDuration);
                }
                //残業代:なければ 0のcarboninterval,あれば計算する。

                if ($curOvertime == null) {
                    $overtimeDuration = CarbonInterval::seconds(0);
                } else {
                    $overtimeStart = Carbon::parse($curOvertime->start_time)->ceilMinute(15);
                    $overtimeEnd = Carbon::parse($curOvertime->end_time)->floorMinute(15);
                    $overtimeDuration = $overtimeStart->diff($overtimeEnd);
                }

                // duration - 休憩の合計 + 残業の時間
                $workDuration = $checkInTimeForCalc->diff($checkOutTimeForCalc);
                $workDurationInterval = CarbonInterval::instance($workDuration);
                $overTimeInterval = CarbonInterval::instance($overtimeDuration);
                $restInterval = CarbonInterval::instance($totalRestDuration);
                $workDurationInterval = $workDurationInterval->add($overTimeInterval)->sub($restInterval);
                // dd($workDurationInterval);
                if ($curAttendance->is_overtime === 1) {
                    $is_overtime_str = "有";
                } else {
                    $is_overtime_str = "無";
                }

                $admin_comments = [];

                // 複数のadminコメントをつなげて1つのテキストにする 各種配列を作$restTimeString = implode("<br>", $restTimes);
                $curAdminComments = $curAttendance->adminComments;
                foreach ($curAdminComments as $curAdminComment) {
                    $admin_comments[] = $curAdminComment->admin == null ? "" : $curAdminComment->admin_description . " :" . $curAdminComment->admin_comment . " (" . $curAdminComment->admin->full_name . ")";
                }

                $admin_comment = implode("<br>", $admin_comments);


                $curAttendanceObj = [
                    'attendance_id' => $curAttendance->id,
                    'date' => $workSchedule->date,
                    'scheduleType' => $workSchedule->specialSchedule == null ? $workSchedule->scheduleType->name : $workSchedule->specialSchedule->schedule_type->name,
                    'bodyTemp' => $curAttendance->body_temp,
                    'checkin' => Carbon::parse($curAttendance->check_in_time)->format('H:i'),
                    'checkout' => $curAttendance->check_out_time == null ? "" : Carbon::parse($curAttendance->check_out_time)->format('H:i'),
                    'is_overtime' => $is_overtime_str,
                    'rest' => $restTimeString,
                    'overtime' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
                    'duration' => $workDurationInterval->format('%H:%I:%S'),
                    'workDescription' => $curAttendance->work_description,
                    'workComment' => $curAttendance->work_comment,
                    'admin_comment' => $admin_comment,
                ];
                array_push($monthlyAttendanceData, $curAttendanceObj);
            }
        }

        return view('admin.attendances.admintimecard', compact('monthlyAttendanceData', 'year', 'month', 'users', 'user_id'));
    }



    public function showUsers()
    {
        $adminId = Auth::id();
        $admin = Admin::with('adminDetail')->find($adminId);
        $companyId = $admin->adminDetail->company_id;
        $users = User::whereHas('userDetail', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['userDetail.disabilityCategory', 'userDetail.residence', 'userDetail.counselor'])->get();

        $userInfoArray = [];
        foreach ($users as $user) {

            $curUserInfo = [
                'beneficiary_number' => $user->userDetail->beneficiary_number,
                'name' => $user->full_name,
                'email' => $user->email,
                'is_on_welfare' => $user->userDetail->is_on_welfare == 1 ? "有" : "無",
                'admission_date' => $user->userDetail->admission_date,
                'discharge_date' => $user->userDetail->discharge_date,
                'birthdate' => $user->userDetail->birthdate,
                'disability_category_id' => $user->userDetail->disabilityCategory->name,
                'residence_id' => $user->userDetail->residence->name,
                'counselor_id' => $user->userDetail->counselor->name,
                'user_id' => $user->id,

            ];
            array_push($userInfoArray, $curUserInfo);
        }

        return view('admin.attendances.users', compact('userInfoArray'));
    }

    public function createUser()
    {
        $disability_categories = DisabilityCategory::get();
        $residences = Residence::get();
        $counselors = Counselor::get();
        return view('admin.attendances.userscreate', compact('disability_categories', 'residences', 'counselors'));
    }

    public function storeUser(UserRequest $request)
    {
        $adminId = Auth::id();
        $adminDetail = AdminDetail::where('admin_id', $adminId)->first();
        $companyId = $adminDetail->company_id;

        $user = new User();
        $user->last_name = $request->last_name;
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        $userDetail = UserDetail::where('user_id', $user->id)->first();
        $userDetail->birthdate = $request->birthdate;
        $userDetail->beneficiary_number = $request->beneficiary_number;
        $userDetail->disability_category_id = $request->disability_category_id;
        //is_on_welfareの有無をチェック
        $userDetail->is_on_welfare = $request->is_on_welfare == 1 ? 1 : 0;

        $userDetail->residence_id = $request->residence_id;
        $userDetail->counselor_id = $request->counselor_id;
        $userDetail->admission_date = $request->admission_date;
        $userDetail->company_id = $companyId;
        $userDetail->update();

        return $this->showUsers();
    }
    public function editUser($id)
    {
        $disability_categories = DisabilityCategory::get();
        $residences = Residence::get();
        $counselors = Counselor::get();

        $user = User::with(['userDetail.disabilityCategory', 'userDetail.residence', 'userDetail.counselor'])->firstWhere('id', $id);

        return view('admin.attendances.usersedit', compact('disability_categories', 'residences', 'counselors', 'user'));
    }

    public function updateUser(UserRequest $request, $id)
    {
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;

        $user = User::with('userDetail')->firstWhere('id', $id);
        $user->last_name = $request->last_name;
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->update();

        $user->userDetail->beneficiary_number = $request->beneficiary_number;
        $user->userDetail->disability_category_id = $request->disability_category_id;
        $user->userDetail->birthdate = $request->birthdate;
        //is_on_welfareの有無をチェック
        $user->userDetail->is_on_welfare = $request->is_on_welfare == 1 ? 1 : 0;
        $user->userDetail->residence_id = $request->residence_id;
        $user->userDetail->counselor_id = $request->counselor_id;
        $user->userDetail->admission_date = $request->admission_date;
        $user->userDetail->discharge_date = $request->discharge_date;
        $user->userDetail->company_id = $companyId;
        $user->userDetail->update();

        return $this->showUsers();
    }


    public function showDaily($date = null)
    {

        if ($date == null) {
            $today = Carbon::today();
            $selectedDate = $today->year . "-" . sprintf("%02d", $today->month) . "-" . sprintf("%02d", $today->day);
        } else {
            $selectedDate = $date;
        }

        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;

        $selectedWorkSched = WorkSchedule::whereHas('attendances', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['attendances.rests', 'attendances.overtimes', 'attendances.adminComments.admin', 'attendances.user.userDetail'])
            ->where('date', $selectedDate)->first();




        //本日の勤怠レコード一覧を取得
        $selectedAttendances = $selectedWorkSched == null ? $selectedAttendances = null : $selectedWorkSched->attendances;
        $dailyAttendanceData = [];

        if ($selectedAttendances !== null) {

            // AdminComments内で自分がコメントしたレコードが最後に来るようにソートする
            foreach ($selectedWorkSched->attendances as $attendance) {
                $sortedAdminComments = $attendance->adminComments->sortBy(function ($comment) use ($admin) {
                    return $comment->admin_id == $admin->id ? 1 : 0;
                });

                $attendance->adminComments = $sortedAdminComments;
            }

            foreach ($selectedAttendances as $curAttendance) {
                $curAttendance->rests == null ? $curRests = [] : $curRests = $curAttendance->rests;
                $restTimes = [];

                foreach ($curRests as $rest) {
                    $restTimes[] = Carbon::parse($rest->start_time)->format('H:i') . '-' . Carbon::parse($rest->end_time)->format('H:i');
                }
                $restTimeString = implode("<br>", $restTimes);
                $curOvertime = $curAttendance->overtimes->first();

                $curAdminComments = $curAttendance->adminComments;
                // dd($curAdminComments);

                $curAttendanceRecord = [
                    'attendance_id' => $curAttendance->id,
                    'beneficialy_number' => $curAttendance->user->userDetail->beneficiary_number,
                    'name' => $curAttendance->user->full_name,
                    'body_temp' => $curAttendance->body_temp,
                    'check_in_time' => $curAttendance->check_in_time,
                    'check_out_time' => $curAttendance->check_out_time,
                    'rest' => $restTimeString,
                    'over_time' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
                    'work_description' => $curAttendance->work_description,
                    'work_comment' => $curAttendance->work_comment,
                    'admin_comments' => $curAdminComments,
                ];
                // $curAdminComment = $curAttendance->adminComments->first();

                // $curAttendanceRecord = [
                //     'attendance_id' => $curAttendance->id,
                //     'beneficialy_number' => $curAttendance->user->userDetail->beneficiary_number,
                //     'name' => $curAttendance->user->full_name,
                //     'body_temp' => $curAttendance->body_temp,
                //     'check_in_time' => $curAttendance->check_in_time,
                //     'check_out_time' => $curAttendance->check_out_time,
                //     'rest' => $restTimeString,
                //     'over_time' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
                //     'work_description' => $curAttendance->work_description,
                //     'work_comment' => $curAttendance->work_comment,
                //     'admin_description' => $curAdminComment->admin_description,
                //     'admin_comment' => $curAdminComment->admin_comment,
                //     'admin_name' => $curAdminComment->admin == null ? null : $curAdminComment->admin->full_name,
                //     'admin_id' => $curAdminComment->admin == null ? null : $curAdminComment->admin->id,
                // ];

                array_push($dailyAttendanceData, $curAttendanceRecord);
            }
        }

        return view('admin.attendances.daily', compact('dailyAttendanceData', 'selectedDate'));
    }

    public function updateAdminComment(Request $request, AdminComment $admincomment)
    {
        $admin_id = Auth::id();
        $adminComment = AdminComment::with('attendance.work_schedule')->where('id', $admincomment->id)->first();
        $workSchedule = WorkSchedule::where('id', $adminComment->attendance->work_schedule_id)->first();
        $date = $workSchedule->date;

        if ($request->user()->cannot('update', $adminComment)) {
            return redirect()->route('admin.daily', compact('date'))->withErrors('自分のコメント以外は更新できません');
        }

        $adminComment->admin_description = $request->admin_description;
        $adminComment->admin_comment = $request->admin_comment;
        $adminComment->admin_id = $admin_id;
        $adminComment->update();

        return redirect()->route('admin.daily', compact('date'));
    }

    public function storeAdminComment(Request $request, Attendance $attendance)
    {
        $admin_id = Auth::id();
        $workSchedule = WorkSchedule::where('id', $attendance->work_schedule_id)->first();
        $date = $workSchedule->date;

        $adminComment = new AdminComment();
        $adminComment->attendance_id = $attendance->id;
        $adminComment->admin_id = $admin_id;
        $adminComment->admin_description = $request->admin_description;
        $adminComment->admin_comment = $request->admin_comment;
        $adminComment->save();

        return redirect()->route('admin.daily', compact('date'));
    }

    public function showAdmins()
    {
        $adminId = Auth::id();
        $adminDetail = AdminDetail::where('admin_id', $adminId)->first();
        $companyId = $adminDetail->company_id;

        $adminInfoArray = [];

        $admins = Admin::whereHas('adminDetail', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['adminDetail.role'])->get();


        foreach ($admins as $admin) {

            $curAdminInfo = [
                'emp_number' => $admin->adminDetail->emp_number,
                'name' => $admin->full_name,
                'email' => $admin->email,
                'role' => $admin->adminDetail->name,
                'hire_date' => $admin->adminDetail->hire_date,
                'termination_date' => $admin->adminDetail->termination_date,
                'admin_id' => $admin->id,
            ];
            array_push($adminInfoArray, $curAdminInfo);
        }

        return view('admin.attendances.admins', compact('adminInfoArray'));
    }
    public function createAdmin()
    {
        $roles = Role::get();
        return view('admin.attendances.adminscreate', compact('roles'));
    }
    public function storeAdmin(AdminRequest $request)
    {

        $adminId = Auth::id();
        $companyId = AdminDetail::where('admin_id', $adminId)->first()->company_id;

        $admin = new Admin();
        $admin->last_name = $request->last_name;
        $admin->first_name = $request->first_name;
        $admin->email = $request->email;
        $admin->password = $request->password;
        $admin->save();

        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $adminDetail->hire_date = $request->hire_date;
        $adminDetail->emp_number = $request->emp_number;
        $adminDetail->role_id = $request->role_id;
        $adminDetail->company_id = $companyId;
        $adminDetail->update();

        return redirect()->route('admin.admins');
    }
    public function editAdmin($id)
    {
        $roles = Role::get();
        $admin = Admin::with('adminDetail.role')->where('id', $id)->first();

        return view('admin.attendances.adminsedit', compact('admin', 'roles'));
    }

    public function updateAdmin(AdminRequest $request, $id)
    {
        $admin = Admin::with('adminDetail')->firstWhere('id', $id);
        $admin->last_name = $request->last_name;
        $admin->first_name = $request->first_name;
        $admin->email = $request->email;
        $admin->password = $request->password;
        $admin->update();

        $admin->adminDetail->hire_date = $request->hire_date;
        $admin->adminDetail->termination_date = $request->termination_date;
        $admin->adminDetail->emp_number = $request->emp_number;
        $admin->adminDetail->role_id = $request->role_id;
        $admin->adminDetail->update();

        return $this->showAdmins();
    }

    public function showCounselors()
    {
        $adminId = Auth::id();
        $companyId = AdminDetail::where('admin_id', $adminId)->first()->company_id;
        $counselors = Counselor::where('company_id', $companyId)->get();

        return view('admin.attendances.counselors', compact('counselors'));
    }
    public function createCounselor()
    {
        return view('admin.attendances.counselorcreate');
    }
    public function storeCounselor(CounselorRequest $request)
    {
        $adminId = Auth::id();
        $companyId = AdminDetail::where('admin_id', $adminId)->first()->company_id;
        $counselor = new Counselor();
        $counselor->name = $request->name;
        $counselor->contact_phone = $request->contact_phone;
        $counselor->contact_email = $request->contact_email;
        $counselor->company_id = $companyId;
        $counselor->save();
        return $this->showCounselors();
    }

    public function editCounselor($id)
    {
        $counselor = Counselor::where('id', $id)->first();
        return view('admin.attendances.counselorsedit', compact('counselor'));
    }

    public function updateCounselor(CounselorRequest $request, $id)
    {
        $counselor = Counselor::where('id', $id)->first();
        $counselor->name = $request->name;
        $counselor->contact_phone = $request->contact_phone;
        $counselor->contact_email = $request->contact_email;
        $counselor->update();

        return $this->showCounselors();
    }
    public function deleteCounselor($id)
    {
        $counselor = Counselor::where('id', $id)->first();
        $counselor->delete();
        return $this->showCounselors();
    }

    public function showResidences()
    {
        $adminId = Auth::id();
        $companyId = AdminDetail::where('admin_id', $adminId)->first()->company_id;
        $residences = Residence::where('company_id', $companyId)->get();

        return view('admin.attendances.residences', compact('residences'));
    }
    public function createResidence()
    {
        return view('admin.attendances.residencecreate');
    }
    public function storeResidences(ResidenceRequest $request)
    {
        $adminId = Auth::id();
        $companyId = AdminDetail::where('admin_id', $adminId)->first()->company_id;
        $residence = new Residence();
        $residence->name = $request->name;
        $residence->contact_name = $request->contact_name;
        $residence->contact_phone = $request->contact_phone;
        $residence->contact_email = $request->contact_email;
        $residence->company_id = $companyId;
        $residence->save();
        return $this->showResidences();
    }

    public function editResidences($id)
    {
        $residence = Residence::where('id', $id)->first();
        return view('admin.attendances.residencesedit', compact('residence'));
    }
    public function updateResidences(ResidenceRequest $request, $id)
    {
        $residnece = Residence::where('id', $id)->first();
        $residnece->name = $request->name;
        $residnece->contact_name = $request->contact_name;
        $residnece->contact_phone = $request->contact_phone;
        $residnece->contact_email = $request->contact_email;
        $residnece->update();

        return $this->showResidences();
    }
    public function deleteResidences($id)
    {
        $residence = Residence::where('id', $id)->first();
        $residence->delete();
        return $this->showResidences();
    }

    public function showWorkschedules($yearmonth = null)
    {

        $adminId = Auth::id();
        $companyId = AdminDetail::where('admin_id', $adminId)->first()->company_id;

        if ($yearmonth == null) {
            $today = Carbon::today();
            $year = $today->year;
            $month = sprintf("%02d", $today->month);
        } else {
            $yearMonthArr = explode("-", $yearmonth);
            $year = $yearMonthArr[0];
            $month = sprintf("%02d", $yearMonthArr[1]);
        }

        $monthlyWorkScheduleData = [];

        // need  workSchedule.specialSchedule / workSchedule.scheduleType
        $thisMonthWorkSchedules = WorkSchedule::with(['scheduleType', 'specialSchedule'])->whereYear('date', $year)->whereMonth('date', $month)->orderBy('date', 'asc')->get();

        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curScheduleType = $workSchedule->scheduleType;
            $curSpecialSchedule = $workSchedule->specialSchedule;
            $curCarbonDate = Carbon::parse($workSchedule->date);
            $curDay = $curCarbonDate->isoFormat('ddd');

            if (!$curSpecialSchedule) {
                $curScheduleObj = [
                    'id' => $workSchedule->id,
                    'special_sched_id' => "",
                    'date' => $workSchedule->date,
                    'day' => $curDay,
                    'scheduleType' => $curScheduleType->name,
                    'description' => "",
                ];
                array_push($monthlyWorkScheduleData, $curScheduleObj);
            } else {
                $overwriteScheduleType = $curSpecialSchedule->schedule_type;
                // dd($overwriteScheduleType);
                $curScheduleObj = [
                    'id' => $workSchedule->id,
                    'special_sched_id' => $curSpecialSchedule->id,
                    'date' => $workSchedule->date,
                    'day' => $curDay,
                    'scheduleType' => $overwriteScheduleType->name,
                    'description' => $curSpecialSchedule->description,
                ];
                array_push($monthlyWorkScheduleData, $curScheduleObj);
            }
        }
        return view('admin.attendances.workschedule', compact('monthlyWorkScheduleData', 'year', 'month'));
    }

    public function createWorkschedules(Request $request)
    {
        $workSchedule = WorkSchedule::find($request->id);
        $carbonDate = Carbon::parse($workSchedule->date);
        $day = $carbonDate->isoFormat('ddd');
        $targetWorkSchedule = [
            'id' => $workSchedule->id,
            'date' => $workSchedule->date,
            'day' => $day,
        ];

        $scheduleTypes = ScheduleType::all();

        return view('admin.attendances.workschedulecreate', compact('targetWorkSchedule', 'scheduleTypes'));
    }
    public function storeWorkschedules(Request $request)
    {
        $admin = Auth::user();
        $targetWorkSchedule = WorkSchedule::where('id', $request->workSchedule_id)->first();
        $year = $targetWorkSchedule->year;
        $month = sprintf("%02d", $targetWorkSchedule->month);
        $yearmonth = $year . "-" . $month;

        $companyId = $admin->adminDetail->company_id;
        $workSchedule = new SpecialSchedule();
        $workSchedule->company_id = $companyId;
        $workSchedule->work_schedule_id =  $request->workSchedule_id;
        $workSchedule->schedule_type_id = $request->schedule_type_id;
        $workSchedule->description = $request->description;
        $workSchedule->save();
        return redirect()->route('admin.workschedules', compact('yearmonth'));
    }
    public function deleteWorkschedules(Request $request)
    {
        $special_sched = SpecialSchedule::find($request->id);
        $year = $special_sched->work_schedule->year;
        $month = sprintf("%02d", $special_sched->work_schedule->month);
        $yearmonth = $year . "-" . $month;
        $special_sched->delete();
        return redirect()->route('admin.workschedules', compact('yearmonth'));
    }

    //export attendances

    //export users
    public function showExport($yearmonth = null)
    {
        if ($yearmonth == null) {
            $lastMonth = Carbon::today()->subMonth();
            $year = $lastMonth->year;
            $month = sprintf("%02d", $lastMonth->month);
        } else {
            $yearMonthArr = explode("-", $yearmonth);
            $year = $yearMonthArr[0];
            $month = sprintf("%02d", $yearMonthArr[1]);
        }

        $yearStr = strval($year);
        $monthStr = strval($month);

        return view('admin.attendances.exportshow', ['year' => $yearStr, 'month' => $monthStr]);
    }



    public function export(Request $request)
    {
        $yearmonth = $request->yearmonth;
        return Excel::download(new AttendanceExport($yearmonth), 'attendances.xlsx');
    }

    public function editAttendance($id)
    {
        // 体温･出勤 退勤 休憩 残業有無 休憩 残業  勤務時間 作業内容 作業コメント 利用者名 利用者番号 日付 勤務カテゴリ
        // 編集可能: 出退勤 休憩 残業 体温
        // 他は表示のみ
        $attendanceTypes = AttendanceType::all();

        $attendance = Attendance::with(['work_schedule', 'rests', 'overtimes', 'adminComments', 'user.userDetail', 'attendanceType'])->find($id);

        return view('admin.attendances.admintimecardedit', compact('attendance', 'attendanceTypes'));
    }

    public function updateAttendance(Request $request, $id)
    {
        $attendance = Attendance::with(['work_schedule', 'rests', 'overtimes', 'adminComments', 'user.userDetail'])->find($id);
        $attendance->body_temp = $request->body_temp;
        $attendance->check_in_time = $request->check_in_time;
        $attendance->check_out_time = $request->check_out_time;
        $attendance->is_overtime = $request->is_overtime;
        $attendance->attendance_type_id = $request->attendance_type;
        $counter = 1;

        foreach ($attendance->rests as $rest) {
            $restStartKey = "rest_start_" . $counter;
            $restEndKey = "rest_end_" . $counter;
            $rest->start_time = $request->$restStartKey;
            $rest->end_time = $request->$restEndKey;
            $rest->update();
            $counter++;
        }

        $counter = 1;
        foreach ($attendance->overtimes as $overtime) {
            $overtimeStartKey = "overtime_start_" . $counter;
            $overtimeEndKey = "overtime_end_" . $counter;
            $overtime->start_time = $request->$overtimeStartKey;
            $overtime->end_time = $request->$overtimeEndKey;
            $overtime->update();
            $counter++;
        }

        if ($request->rest_start_add) {
            $newRest = new Rest();
            $newRest->attendance_id = $id;
            $newRest->start_time = $request->rest_start_add;
            $newRest->end_time = $request->rest_end_add;
            // dd($newRest);
            $newRest->save();
        }

        if ($request->overtime_start_add) {
            $newOvertime = new Overtime();
            $newOvertime->attendance_id = $id;
            $newOvertime->start_time = $request->overtime_start_add;
            $newOvertime->end_time = $request->overtime_end_add;
            // dd($newRest);
            $newOvertime->save();
        }

        $attendance->update();

        return redirect()->route('admin.attendance.edit', $id);
    }
}
