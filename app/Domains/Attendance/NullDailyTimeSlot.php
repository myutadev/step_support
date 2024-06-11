<?php

namespace App\Domains\Attendance;

use Carbon\CarbonInterval;

class NullDailyTimeSlot implements DailyTimeSlotInterface
{


    public function getAttendanceId(): int
    {
        return "";
    }

    public function getTimeSlots(): array
    {
        return [];
    }

    public function showAllTimeSlotsStr(): string
    {
        return "";
    }

    public function sumTotalDuration(): CarbonInterval
    {
        return CarbonInterval::minutes(0);
    }
}
