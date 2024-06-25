<?php

namespace App\Domains\Attendance;

use Carbon\Carbon;
use DateTime;

class TimeSlot
{
    protected Carbon $startTime;
    protected Carbon $endTime;

    public function __construct(Carbon $startTime, Carbon $endTime)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getStartTime(): Carbon
    {
        return  $this->startTime;
    }

    public function getEndTime(): Carbon
    {
        return $this->endTime;
    }
}
