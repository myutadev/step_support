<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function userDetail()
    {
        return $this->hasMany(userDetail::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function counselors()
    {
        return $this->hasMany(Counselor::class);
    }
    public function special_schedules()
    {
        return $this->hasMany(SpecialSchedule::class);
    }
}
