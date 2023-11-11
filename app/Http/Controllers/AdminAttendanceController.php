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
