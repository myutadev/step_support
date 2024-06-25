<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    protected $user;
    protected $adminRepository;

    public function __construct(User $user, AdminRepository $adminRepository)
    {
        $this->user = $user;
        $this->adminRepository = $adminRepository;
    }

    public function getUsersByCompanyId(int $companyId): Collection
    {
        return $this->user->whereHas('userDetail', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with('userDetail', 'attendances.rests', 'attendances.overtimes')->get();
    }
    public function getFirstUserByCompanyId(int $companyId): User
    {
        return $this->getUsersByCompanyId($companyId)->first();
    }

    public function createNewUser(): User
    {
        return new User();
    }

    public function getUserDetailByUser(User $user): UserDetail
    {
        return UserDetail::where('user_id', $user->id)->first();
    }

    public function getUserWithDetailsByUserId($id): User
    {
        return User::with(['userDetail.disabilityCategory', 'userDetail.residence', 'userDetail.counselor'])->firstWhere('id', $id);
    }

    public function getAllCompanyUsers()
    {
        $companyId = $this->adminRepository->getCurrentCompanyId();

        return  User::whereHas('userDetail', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['userDetail', 'attendances.rests', 'attendances.overtimes'])->get();
    }

    public function getActiveUsers(Carbon $dischargeDateCondition)
    {
        return $this->getAllCompanyUsers()->filter(function ($user) use ($dischargeDateCondition) {
            return $user->userDetail->discharge_date >= $dischargeDateCondition || is_null(
                $user->userDetail->discharge_date
            );
        });
    }

    public function getCurrentUserId()
    {
        return Auth::id();
    }
}
