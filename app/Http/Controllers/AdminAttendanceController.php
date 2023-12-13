<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\UserRequest;
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
use App\Models\UserDetail;
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
}
