<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleType extends Model
{
    use HasFactory;

    public function workSchedule()
    {
        return $this->hasMany(WorkSchedule::class);
    }
    public function specialSchedule()
    {
        return $this->hasMany(SpecialSchedule::class);
    }
}
