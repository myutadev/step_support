<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Rest;
use App\Models\ScheduleType;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WorkSchedule;
use App\Models\Admin;
use App\Models\AdminComment;
use App\Models\AdminDetail;
use App\Models\Counselor;
use App\Models\DisabilityCategory;
use App\Models\Residence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;

use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{

    public function showTimecard()
    {
        $monthlyAttendanceData = [];

        return view('admin.attendances.admintimecard', compact('monthlyAttendanceData'));
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
                'disability_category_id' => DisabilityCategory::where('id', $userDetail->disability_category_id)->first()->name,
                'residence_id' => Residence::where('id', $userDetail->residence_id)->first()->name,
                'counselor_id' => Counselor::where('id', $userDetail->counselor_id)->first()->name,
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
        $couselors = Counselor::get();
        return view('admin.attendances.userscreate', compact('disability_categories', 'residences', 'couselors'));
    }
    public function storeUser(Request $request)
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



    public function showDaily()
    {

        $admin = Auth::user();
        $adminDetail = AdminDetail::where('admin_id', $admin->id)->first();
        $companyId = $adminDetail->company_id;
        $today = Carbon::today();
        $todayWorkSchedId = WorkSchedule::where('date', $today)->first()->id;

        //本日の勤怠レコード一覧を取得
        $attendanceRecords = Attendance::where('company_id', $companyId)->get()->where('work_schedule_id', $todayWorkSchedId);

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
                'beneficialy_number' => UserDetail::where('user_id', $curUserId)->first()->beneficiary_number,
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
                'admin_id' => $admin_id,
            ];

            array_push($dailyAttendanceData, $curAttendanceRecord);
        }

        return view('admin.attendances.daily', compact('dailyAttendanceData'));
    }

    public function updateAdminComment(Request $request, Attendance $attendance)
    {
        $admin_id = Auth::id();
        $adminComment = AdminComment::where('attendance_id', $attendance->id)->first();
        $adminComment->admin_description = $request->admin_description;
        $adminComment->admin_comment = $request->admin_comment;
        $adminComment->admin_id = $admin_id;
        $adminComment->update();

        return $this->showDaily();
    }

    public function showAdmins()
    {
    }
    public function createAdmin()
    {
    }
    public function storeAdmins()
    {
    }
}
