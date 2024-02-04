<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    public function scheduleType()
    {
        return $this->belongsTo(ScheduleType::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function specialSchedule()
    {
        return $this->hasMany(SpecialSchedule::class);
    }
}
