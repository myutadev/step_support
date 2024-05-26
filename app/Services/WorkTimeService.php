<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class WorkTimeService
{
    //これらはビジネスロジック
    public function calculateWorkDuration(
        $check_in_time,
        $check_out_time,
        $is_overtime,
        $rests, // array[obj1,obj2]
        $overtime, // object
    ) {

        //ここから1日の勤務時間の計算 1. 出勤 10時以前→10時、10時以降→15分単位で切り上げ
        $checkInTimeForCalc = Carbon::parse($check_in_time);
        $checkOutTimeForCalc = Carbon::parse($check_out_time);
        $baseTimeForIn = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 10:00:00');
        $baseTimeForOut = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 15:00:00');

        $isOvertime = $is_overtime;

        //出勤時間の切り上げ
        if ($checkInTimeForCalc->lt($baseTimeForIn)) {
            $checkInTimeForCalc->hour(10)->minute(0)->second(0);
        } else {
            $checkInTimeForCalc->ceilMinute(15);
        }


        //退勤時間の切り下げ 残業なし(isOvertime=0) かつ 15時以降の打刻であれば
        if ($checkOutTimeForCalc->gt($baseTimeForOut) && $isOvertime == 0) {
            $checkOutTimeForCalc->hour(15)->minute(0)->second(0);
        } else {
            $checkOutTimeForCalc->floorminute(15);
        }

        $totalRestDuration = CarbonInterval::seconds(0); // 0秒で初期化

        foreach ($rests as $rest) {
            $restStart = Carbon::parse($rest->start_time);
            $restEnd = Carbon::parse($rest->end_time);
            $restDuration = $restStart->floorminute(15)->diff($restEnd->ceilminute(15));

            $totalRestDuration = $totalRestDuration->add($restDuration);
        }
        //残業代:なければ 0のcarboninterval,あれば計算する。

        if ($overtime == null) {
            $overtimeDuration = CarbonInterval::seconds(0);
        } else {
            $overtimeStart = Carbon::parse($overtime->start_time)->ceilMinute(15);
            $overtimeEnd = Carbon::parse($overtime->end_time)->floorMinute(15);
            $overtimeDuration = $overtimeStart->diff($overtimeEnd);
        }

        // duration - 休憩の合計 + 残業の時間
        $workDuration = $checkInTimeForCalc->diff($checkOutTimeForCalc);
        $workDurationInterval = CarbonInterval::instance($workDuration);
        $overTimeInterval = CarbonInterval::instance($overtimeDuration);
        $restInterval = CarbonInterval::instance($totalRestDuration);
        $workDurationInterval =
            $workDurationInterval->add($overTimeInterval)->sub($restInterval);

        return $workDurationInterval;
    }

    //これらは一般的な機能

    public static function convertDaysToHours(CarbonInterval $interval)
    {
        $totalHours = $interval->d * 24 + $interval->hours;

        $newInterval = CarbonInterval::hours($totalHours)
            ->minutes($interval->minutes)
            ->seconds($interval->seconds);

        $newInterval->invert = $interval->invert == 0 ? 0 : 1;

        return $newInterval;
    }

    public static function convertSecondsToHours(CarbonInterval $interval)
    {
        $totalHours = ($interval->s / 60 / 60) + $interval->hours;

        $newInterval = CarbonInterval::hours($totalHours);

        $newInterval->invert = $interval->invert == 0 ? 0 : 1;

        return $newInterval;
    }
};
