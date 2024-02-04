<?php

namespace App\Http\Controllers;

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

class AdminAttendanceController extends Controller
{

    public function showTimecard($yearmonth = null, $user_id = null)
    {
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $userDetails = UserDetail::with('user')->where('company_id', $companyId)->get();
        $users = $userDetails->map(function ($userDetail) {
            return [
                'id' => $userDetail->user_id,
                'name' => $userDetail->user->last_name . $userDetail->user->first_name,
            ];
        });
        // dd($users);

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
        // dd($user_name);
        // 表示データの作成
        $monthlyAttendanceData = [];
        $thisMonthWorkSchedules = WorkSchedule::whereYear('date', $year)->whereMonth('date', $month)->orderBy('date', 'asc')->get();
        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curScheduleType = ScheduleType::where('id', $workSchedule->schedule_type_id)->first();
            $curAttendance = Attendance::where('user_id', $user_id)->where('work_schedule_id', $workSchedule->id)->first();

            if (!$curAttendance) {
                $curAttendanceObj = [
                    'date' => $workSchedule->date,
                    'scheduleType' => $curScheduleType->name,
                    'bodyTemp' => "",
                    'checkin' => "",
                    'checkout' => "",
                    'is_overtime' => "",
                    'rest' => "",
                    'overtime' => "",
                    'duration' => "",
                    'workDescription' => "",
                    'workComment' => "",
                ];
                array_push($monthlyAttendanceData, $curAttendanceObj);
            } else {
                $curRests = Rest::where('attendance_id', $curAttendance->id)->get();
                //休憩は複数回入る可能性あり。
                $restTimes = [];
                foreach ($curRests as $rest) {
                    $restTimes[] = Carbon::parse($rest->start_time)->format('H:i') . '-' . Carbon::parse($rest->end_time)->format('H:i');
                }
                $restTimeString = implode("<br>", $restTimes);

                $curOvertime = Overtime::where('attendance_id', $curAttendance->id)->first();

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

                $curAttendanceObj = [
                    'date' => $workSchedule->date,
                    'scheduleType' => $curScheduleType->name,
                    'bodyTemp' => $curAttendance->body_temp,
                    'checkin' => Carbon::parse($curAttendance->check_in_time)->format('H:i'),
                    'checkout' => $curAttendance->check_out_time == null ? "" : Carbon::parse($curAttendance->check_out_time)->format('H:i'),
                    'is_overtime' => $is_overtime_str,
                    'rest' => $restTimeString,
                    'overtime' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
                    'duration' => $workDurationInterval->format('%H:%I:%S'),
                    'workDescription' => $curAttendance->work_description,
                    'workComment' => $curAttendance->work_comment,
                ];
                array_push($monthlyAttendanceData, $curAttendanceObj);
            }
        }

        return view('admin.attendances.admintimecard', compact('monthlyAttendanceData', 'year', 'month', 'users', 'user_id'));
    }

    public function showUsers()
    {

        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $userDetails = UserDetail::where('company_id', $companyId)->get();
        $userInfoArray = [];
        foreach ($userDetails as $userDetail) {
            $curUser = User::where('id', $userDetail->user_id)->first();

            $curUserInfo = [
                'beneficiary_number' => $userDetail->beneficiary_number,
                'name' => $curUser->last_name . ' ' . $curUser->first_name,
                'email' => $curUser->email,
                'is_on_welfare' => $userDetail->is_on_welfare == 1 ? "有" : "無",
                'admission_date' => $userDetail->admission_date,
                'discharge_date' => $userDetail->discharge_date,
                'birthdate' => $userDetail->birthdate,
                'disability_category_id' => DisabilityCategory::where('id', $userDetail->disability_category_id)->first()->name,
                'residence_id' => Residence::where('id', $userDetail->residence_id)->first()->name,
                'counselor_id' => Counselor::where('id', $userDetail->counselor_id)->first()->name,
                'user_id' => $curUser->id,
            ];
            array_push($userInfoArray, $curUserInfo);
        }
        // dd($userInfoArray);

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
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
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
        $user = User::firstWhere('id', $id);
        $userDetail = UserDetail::firstWhere('user_id', $id);
        $disability_categories = DisabilityCategory::get();
        $residences = Residence::get();
        $counselors = Counselor::get();

        return view('admin.attendances.usersedit', compact('disability_categories', 'residences', 'counselors', 'user', 'userDetail'));
    }

    public function updateUser(UserRequest $request, $id)
    {
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;

        $user = User::firstWhere('id', $id);
        $user->last_name = $request->last_name;
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->update();

        $userDetail = UserDetail::firstWhere('user_id', $id);
        $userDetail->beneficiary_number = $request->beneficiary_number;
        $userDetail->disability_category_id = $request->disability_category_id;
        $userDetail->birthdate = $request->birthdate;
        //is_on_welfareの有無をチェック
        $userDetail->is_on_welfare = $request->is_on_welfare == 1 ? 1 : 0;
        $userDetail->residence_id = $request->residence_id;
        $userDetail->counselor_id = $request->counselor_id;
        $userDetail->admission_date = $request->admission_date;
        $userDetail->discharge_date = $request->discharge_date;
        $userDetail->company_id = $companyId;
        $userDetail->update();

        return $this->showUsers();
    }



    public function showDaily($date = null)
    {

        if ($date == null) {
            $selectedDate = Carbon::today();
        } else {
            $selectedDate = $date;
        }

        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $selectedWorkSchedId = WorkSchedule::where('date', $selectedDate)->first()->id;

        //本日の勤怠レコード一覧を取得
        $attendanceRecords = Attendance::where('company_id', $companyId)->get()->where('work_schedule_id', $selectedWorkSchedId);

        $dailyAttendanceData = [];

        foreach ($attendanceRecords as $curAttendance) {
            $admin_id = Auth::id();
            $admin = Admin::where('id', $admin_id)->first();
            $admin_name = $admin->last_name . ' ' . $admin->first_name;
            $curUserId = $curAttendance->user_id;
            $curUser = User::where('id', $curUserId)->first();
            $curRests = Rest::where('attendance_id', $curAttendance->id)->get();
            $restTimes = [];

            foreach ($curRests as $rest) {
                $restTimes[] = Carbon::parse($rest->start_time)->format('H:i') . '-' . Carbon::parse($rest->end_time)->format('H:i');
            }
            $restTimeString = implode("<br>", $restTimes);

            $curOvertime = Overtime::where('attendance_id', $curAttendance->id)->first();

            $curAdminComment = AdminComment::where('attendance_id', $curAttendance->id)->first();

            $curAttendanceRecord = [
                'attendance_id' => $curAttendance->id,
                'beneficialy_number' => userDetail::where('user_id', $curUserId)->first()->beneficiary_number,
                'name' => $curUser->last_name . " " . $curUser->first_name,
                'body_temp' => $curAttendance->body_temp,
                'check_in_time' => $curAttendance->check_in_time,
                'check_out_time' => $curAttendance->check_out_time,
                'rest' => $restTimeString,
                'over_time' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
                'work_description' => $curAttendance->work_description,
                'work_comment' => $curAttendance->work_comment,
                'admin_description' => $curAdminComment->admin_description,
                'admin_comment' => $curAdminComment->admin_comment,
                'admin_name' => $admin_name,
            ];

            array_push($dailyAttendanceData, $curAttendanceRecord);
        }

        return view('admin.attendances.daily', compact('dailyAttendanceData', 'selectedDate'));
    }

    public function updateAdminComment(Request $request, Attendance $attendance)
    {
        $admin_id = Auth::id();
        $adminComment = AdminComment::where('attendance_id', $attendance->id)->first();
        $adminComment->admin_description = $request->admin_description;
        $adminComment->admin_comment = $request->admin_comment;
        $adminComment->admin_id = $admin_id;
        $adminComment->update();

        $workSchedule = WorkSchedule::where('id', $attendance->work_schedule_id)->first();
        $date = $workSchedule->date;
        return redirect()->route('admin.daily', compact('date'));
    }

    public function showAdmins()
    {
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $adminDetails = AdminDetail::where('company_id', $companyId)->get();
        $adminInfoArray = [];
        foreach ($adminDetails as $adminDetail) {
            $curAdmin = Admin::where('id', $adminDetail->admin_id)->first();

            $curAdminInfo = [
                'emp_number' => $adminDetail->emp_number,
                'name' => $curAdmin->last_name . ' ' . $curAdmin->first_name,
                'email' => $curAdmin->email,
                'role' => Role::Firstwhere('id', $adminDetail->role_id)->name,
                'hire_date' => $adminDetail->hire_date,
                'termination_date' => $adminDetail->termination_date,
                'admin_id' => $curAdmin->id,
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

        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;

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

        return $this->createAdmin();
    }
    public function editAdmin($id)
    {
        $admin = Admin::firstWhere('id', $id);
        $adminDetail = AdminDetail::firstWhere('admin_id', $id);
        $roles = Role::get();
        $roleId = $adminDetail->role_id;
        $role = Role::firstWhere('id', $roleId);

        return view('admin.attendances.adminsedit', compact('admin', 'adminDetail', 'role', 'roles'));
    }

    public function updateAdmin(AdminRequest $request, $id)
    {
        $admin = Admin::firstWhere('id', $id);
        $admin->last_name = $request->last_name;
        $admin->first_name = $request->first_name;
        $admin->email = $request->email;
        $admin->password = $request->password;
        $admin->update();

        $adminDetail = AdminDetail::firstWhere('admin_id', $admin->id);
        $adminDetail->hire_date = $request->hire_date;
        $adminDetail->termination_date = $request->termination_date;
        $adminDetail->emp_number = $request->emp_number;
        $adminDetail->role_id = $request->role_id;
        $adminDetail->update();

        return $this->showAdmins();
    }

    public function showCounselors()
    {
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $counselors = Counselor::where('company_id', $companyId)->get();

        return view('admin.attendances.counselors', compact('counselors'));
    }
    public function createCounselor()
    {
        return view('admin.attendances.counselorcreate');
    }
    public function storeCounselor(CounselorRequest $request)
    {
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
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
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $residences = Residence::where('company_id', $companyId)->get();

        return view('admin.attendances.residences', compact('residences'));
    }
    public function createResidence()
    {
        return view('admin.attendances.residencecreate');
    }
    public function storeResidences(ResidenceRequest $request)
    {
        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
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

        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $schedules = SpecialSchedule::where('company_id', $companyId)->get();
        // dd($schedules);
        $residences = Residence::where('company_id', $companyId)->get();

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
        $thisMonthWorkSchedules = WorkSchedule::whereYear('date', $year)->whereMonth('date', $month)->orderBy('date', 'asc')->get();

        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curScheduleType = ScheduleType::where('id', $workSchedule->schedule_type_id)->first();
            $curSpecialSchedule = SpecialSchedule::where('work_schedule_id', $workSchedule->id)->first();
            $curCarbonDate = Carbon::parse($workSchedule->date);
            $curDay = $curCarbonDate->isoFormat('ddd');

            if (!$curSpecialSchedule) {
                $curScheduleObj = [
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
}
