<?php

namespace App\Domains;

use App\Models\User;
use App\Services\WorkTimeService;
use App\Utils\TimeFormatter;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;

class UserAttendanceRange
{
    protected $user;
    protected $attendances;

    public function __construct(User $user, Collection $attendances)
    {
        $this->user = $user;
        $this->attendances = $attendances;
    }

    public static function create(User $user, int $firstWorkScheduleId, int $lastWorkScheduleId)
    {
        if (!$user->relationLoaded('attendances')) {
            $user->load('attendances');
        }

        $attendances = $user->attendances;
        $filteredAttendances =
            $attendances->filter(function ($attendance) use ($firstWorkScheduleId, $lastWorkScheduleId) {
                return
                    $attendance->work_schedule_id >= $firstWorkScheduleId
                    && $attendance->work_schedule_id <= $lastWorkScheduleId;
            });

        return new self($user, $filteredAttendances);
    }

    public function getPresentAttendances(): Collection
    {
        return $this->attendances->filter(function ($attendance) {
            return ($attendance->attendance_type_id == 1 || $attendance->attendance_type_id == 2);
        });
    }

    public function getPresentCount(): int
    {
        $presentAttendances = $this->getPresentAttendances();
        return count($presentAttendances);
    }

    public function getPresentRate(int $openingSoFarThisMonth): int
    {
        //未来月選択の場合 opening SoFarが0になる
        if ($openingSoFarThisMonth == 0) return 0;

        $presentCount = $this->getPresentCount();
        return intval(($presentCount / $openingSoFarThisMonth) * 100);
    }

    public function getTotalWorkDurationInterval(): CarbonInterval
    {
        $thisMonthAttendancesByUser = $this->getPresentAttendances();
        $totalWorkDurationInterval = CarbonInterval::seconds(0);
        foreach ($thisMonthAttendancesByUser as $attendance) {

            $checkInTimeForCalc = Carbon::parse($attendance->check_in_time);
            $checkOutTimeForCalc = Carbon::parse($attendance->check_out_time);
            $baseTimeForIn = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 10:00:00');
            $baseTimeForOut = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 15:00:00');

            //出勤時間の切り上げ
            if ($checkInTimeForCalc->lt($baseTimeForIn)) {
                $checkInTimeForCalc->hour(10)->minute(0)->second(0);
            } else {
                $checkInTimeForCalc->ceilMinute(15);
            }

            //退勤時間の切り下げ 残業なし(isOvertime=0) かつ 15時以降の打刻であれば
            if ($checkOutTimeForCalc->gt($baseTimeForOut) && $attendance->is_overtime == 0) {
                $checkOutTimeForCalc->hour(15)->minute(0)->second(0);
            } else {
                $checkOutTimeForCalc->floorminute(15);
            }

            $totalRestDuration = CarbonInterval::seconds(0); // 0秒で初期化

            foreach ($attendance->rests as $rest) {
                $restStart = Carbon::parse($rest->start_time);
                $restEnd = Carbon::parse($rest->end_time);
                $restDuration = $restStart->floorminute(15)->diff($restEnd->ceilminute(15));

                $totalRestDuration = $totalRestDuration->add($restDuration);
            }

            //残業代:なければ 0のcarboninterval,あれば計算する。

            if ($attendance->overtime == null) {
                $overtimeDuration = CarbonInterval::seconds(0);
            } else {
                $overtimeStart = Carbon::parse($attendance->overtime->start_time)->ceilMinute(15);
                $overtimeEnd = Carbon::parse($attendance->overtime->end_time)->floorMinute(15);
                $overtimeDuration = $overtimeStart->diff($overtimeEnd);
            }
            $workDuration = $checkInTimeForCalc->diff($checkOutTimeForCalc);
            $workDurationInterval = CarbonInterval::instance($workDuration);
            $overTimeInterval = CarbonInterval::instance($overtimeDuration);
            $restInterval = CarbonInterval::instance($totalRestDuration);
            $workDurationInterval =
                $workDurationInterval->add($overTimeInterval)->sub($restInterval);

            $totalWorkDurationInterval->add($workDurationInterval);
        }
        return $totalWorkDurationInterval;
    }

    public function getFormattedTotalWorkDuration(): string
    {
        $totalWorkDurationInterval = $this->getTotalWorkDurationInterval();
        return TimeFormatter::convertDaysToHours($totalWorkDurationInterval->cascade())->format('%H:%I:%S');
    }
    public function getRestToAchieveTarget(int $targetHour): CarbonInterval
    {
        $TARGET_HOURS = CarbonInterval::hours($targetHour);

        $totalWorkDurationInterval = $this->getTotalWorkDurationInterval();
        $restToAchieveTargetByDays = $totalWorkDurationInterval->sub($TARGET_HOURS)->cascade();

        return TimeFormatter::convertDaysToHours($restToAchieveTargetByDays);
    }
}
