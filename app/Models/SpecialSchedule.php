<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialSchedule extends Model
{
    use HasFactory;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function schedule_type()
    {
        return $this->belongsTo(ScheduleType::class);
    }
    public function work_schedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
