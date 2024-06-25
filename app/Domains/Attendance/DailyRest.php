<?php

namespace App\Domains\Attendance;

class DailyRest
{
    protected DailyTimeSlot $dailyTimeSlot;

    public function __construct(DailyTimeSlot $dailyTimeSlot = null)
    {
        $this->dailyTimeSlot = $dailyTimeSlot ?: new NullDailyTimeSlot;
    }

    public function getDailyTimeSlot(): DailyTimeSlot
    {
        return $this->dailyTimeSlot;
    }
}
