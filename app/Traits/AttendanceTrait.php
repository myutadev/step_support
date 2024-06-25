<?php

namespace App\Traits;

use Carbon\Carbon;

trait AttendanceTrait
{
    public function generateStartEndString($timeSlots)
    {
        $timeSlotTexts = [];
        foreach ($timeSlots as $timeSlot) {
            $timeSlotTexts[] = Carbon::parse(
                $timeSlot->start_time
            )->format('H:i') . '-' .
                Carbon::parse($timeSlot->end_time)->format('H:i');
        }
        return  implode("<br>", $timeSlotTexts);
    }
}
