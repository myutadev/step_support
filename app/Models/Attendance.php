<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }
    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function work_schedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
