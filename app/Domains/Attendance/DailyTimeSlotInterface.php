<?php

namespace App\Domains\Attendance;

use Carbon\CarbonInterval;

interface DailyTimeSlotInterface
{
    public function getAttendanceId(): int;
    public function getTimeSlots(): array;
    public function showAllTimeSlotsStr(): string;
    public function sumTotalDuration(): CarbonInterval;
}
