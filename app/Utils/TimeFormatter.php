<?php

namespace App\Utils;

use Carbon\CarbonInterval;

class TimeFormatter
{

    public static function carbonIntervalToStringHours(CarbonInterval $interval)
    {
        return $interval->cascade()->format('%H:%I:%S');
    }

    public static function convertDaysToHours(CarbonInterval $interval): CarbonInterval
    {
        $totalHours = $interval->d * 24 + $interval->hours;

        $newInterval = CarbonInterval::hours($totalHours)
            ->minutes($interval->minutes)
            ->seconds($interval->seconds);

        $newInterval->invert = $interval->invert == 0 ? 0 : 1;

        return $newInterval;
    }

    public static function convertSecondsToHours(CarbonInterval $interval): CarbonInterval
    {
        $totalHours = ($interval->s / 60 / 60) + $interval->hours;

        $newInterval = CarbonInterval::hours($totalHours);

        $newInterval->invert = $interval->invert == 0 ? 0 : 1;

        return $newInterval;
    }
}
