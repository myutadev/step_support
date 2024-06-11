<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserService
{

    public function getCompanyUsers(): Collection
    {
        $adminId = Auth::id();
        $admin = Admin::with('adminDetail')->find($adminId);
        $companyId = $admin->adminDetail->company_id;
        return  User::whereHas('userDetail', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['userDetail', 'attendances.rests', 'attendances.overtimes'])->get();
    }

    function getActiveUsers($allCompanyUsers, $dischargeDateCondition)
    {
        return $allCompanyUsers->filter(function ($user) use ($dischargeDateCondition) {
            return $user->userDetail->discharge_date >= $dischargeDateCondition || is_null(
                $user->userDetail->discharge_date
            );
        });
    }
}
