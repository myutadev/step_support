<?php

namespace App\Domains;

use App\Services\WorkTimeService;
use App\Utils\TimeFormatter;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;

class WholeCompanyAttendance
{
    protected $users;
    protected $userAttendanceRange;
    protected  $NUMBER_OF_TOTAL_FACILITY = 2;
    protected  $CLAIM_HOURS_PER_USER = 4;
    protected  $USERS_PER_FACILITY = 18;
    protected  $ACCEPT_OVER_USERS_RATE = 1.25;


    public function __construct(Collection $users, UserAttendanceRange $userAttendanceRange)
    {
        $this->users = $users;
        $this->userAttendanceRange = $userAttendanceRange;
    }

    public function getCompanyTotalWorkDurationInterval(int $firstWorkScheduleId, int $lastWorkScheduleId): CarbonInterval
    {
        $companyTotalWorkDurationInterval = CarbonInterval::seconds(0);
        foreach ($this->users as $user) {
            $userAttendanceRange = $this->userAttendanceRange->create($user, $firstWorkScheduleId, $lastWorkScheduleId);
            $companyTotalWorkDurationInterval->add($userAttendanceRange->getTotalWorkDurationInterval());
        }

        return $companyTotalWorkDurationInterval;
    }

    public function getCompanyTotalWorkDuration(int $firstWorkScheduleId, int $lastWorkScheduleId): string
    {
        $companyTotalWorkDurationInterval = $this->getCompanyTotalWorkDurationInterval($firstWorkScheduleId, $lastWorkScheduleId);
        return TimeFormatter::convertDaysToHours($companyTotalWorkDurationInterval->cascade())->format('%H:%I:%S');
    }

    public function getTotalClaimCount(int $firstWorkScheduleId, int $lastWorkScheduleId): int
    {
        $totalClaimsCount = 0;

        foreach ($this->users as $user) {
            $userAttendanceRange = $this->userAttendanceRange->create($user, $firstWorkScheduleId, $lastWorkScheduleId);
            $totalClaimsCount += $userAttendanceRange->getPresentCount();
        }
        return $totalClaimsCount;
    }


    public function getTargetTotalWorkDurationInterval(int $TARGET_HOURS, int $firstWorkScheduleId, int $lastWorkScheduleId)
    {
        $targetClaimSecondsPerPerson = CarbonInterval::hours($TARGET_HOURS)->totalSeconds;
        $targetTotalWorkDurationSeconds = $targetClaimSecondsPerPerson * $this->getTotalClaimCount($firstWorkScheduleId, $lastWorkScheduleId);
        $targetTotalWorkDurationInterval = CarbonInterval::seconds($targetTotalWorkDurationSeconds);
        return $targetTotalWorkDurationInterval;
    }
    public function getTargetTotalWorkDuration(int $TARGET_HOURS, int $firstWorkScheduleId, int $lastWorkScheduleId)
    {
        $targetTotalWorkDurationInterval = $this->getTargetTotalWorkDurationInterval($TARGET_HOURS,  $firstWorkScheduleId, $lastWorkScheduleId);

        $targetTotalWorkDuration = TimeFormatter::convertDaysToHours($targetTotalWorkDurationInterval->cascade())->format('%H:%I:%S');

        return $targetTotalWorkDuration;
    }
    public function getRestToAchieveCompanyTarget(int $TARGET_HOURS, int $firstWorkScheduleId, int $lastWorkScheduleId)
    {
        $companyTotalWorkDurationInterval = $this->getCompanyTotalWorkDurationInterval($firstWorkScheduleId, $lastWorkScheduleId);
        // dump('companyTotalWorkDurationInterval', $companyTotalWorkDurationInterval);
        $targetTotalWorkDurationInterval = $this->getTargetTotalWorkDurationInterval($TARGET_HOURS,  $firstWorkScheduleId, $lastWorkScheduleId);
        // dump('restToAchieveCompanyTargetInterval', $targetTotalWorkDurationInterval);

        $restToAchieveCompanyTargetInterval = $companyTotalWorkDurationInterval->sub($targetTotalWorkDurationInterval)->cascade();
        $restToAchieveCompanyTarget =
            $restToAchieveCompanyTargetInterval->invert == 1 ?
            "-" . TimeFormatter::convertDaysToHours($restToAchieveCompanyTargetInterval->cascade())->format('%H:%I:%S')
            : TimeFormatter::convertDaysToHours($restToAchieveCompanyTargetInterval->cascade())->format('%H:%I:%S');

        return $restToAchieveCompanyTarget;
    }

    public function getMaxTotalWorkDuration(int $openingSoFarThisMonth): string
    {
        $maxUsersPerFacility = $this->USERS_PER_FACILITY * $this->ACCEPT_OVER_USERS_RATE;

        $maxClaimHoursPerFacility = $maxUsersPerFacility * $this->CLAIM_HOURS_PER_USER;
        $maxClaimHoursPerCompany = $maxClaimHoursPerFacility * $this->NUMBER_OF_TOTAL_FACILITY;

        $maxClaimSecondsPerDay = CarbonInterval::hours($maxClaimHoursPerCompany)->totalSeconds;
        $maxTotalWorkDurationSeconds = $maxClaimSecondsPerDay * $openingSoFarThisMonth;

        $maxTotalWorkDuration = TimeFormatter::convertSecondsToHours(CarbonInterval::seconds($maxTotalWorkDurationSeconds))->format('%H:%I:%S');
        return $maxTotalWorkDuration;
    }
}
