<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
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

}
