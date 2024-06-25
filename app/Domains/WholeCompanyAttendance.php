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
    protected $NUMBER_OF_TOTAL_FACILITY = 2;
    protected $CLAIM_HOURS_PER_USER = 4;
    protected $USERS_PER_FACILITY = 18;
    protected $ACCEPT_OVER_USERS_RATE = 1.25;
    protected $firstWorkScheduleId;
    protected $lastWorkScheduleId;


    public function __construct(Collection $users, UserAttendanceRange $userAttendanceRange, $firstWorkScheduleId, $lastWorkScheduleId)
    {
        $this->users = $users;
        $this->userAttendanceRange = $userAttendanceRange;
        $this->firstWorkScheduleId = $firstWorkScheduleId;
        $this->lastWorkScheduleId = $lastWorkScheduleId;
    }

    public function getCompanyTotalWorkDurationInterval(): CarbonInterval
    {
        $companyTotalWorkDurationInterval = CarbonInterval::seconds(0);
        foreach ($this->users as $user) {
            $userAttendanceRange = $this->userAttendanceRange->create($user, $this->firstWorkScheduleId, $this->lastWorkScheduleId);
            $companyTotalWorkDurationInterval->add($userAttendanceRange->getTotalWorkDurationInterval());
        }

        return $companyTotalWorkDurationInterval;
    }

    public function getCompanyTotalWorkDuration(): string
    {
        $companyTotalWorkDurationInterval = $this->getCompanyTotalWorkDurationInterval($this->firstWorkScheduleId, $this->lastWorkScheduleId);
        return TimeFormatter::convertDaysToHours($companyTotalWorkDurationInterval->cascade())->format('%H:%I:%S');
    }

    public function getTotalClaimCount(): int
    {
        $totalClaimsCount = 0;

        foreach ($this->users as $user) {
            $userAttendanceRange = $this->userAttendanceRange->create($user, $this->firstWorkScheduleId, $this->lastWorkScheduleId);
            $totalClaimsCount += $userAttendanceRange->getPresentCount();
        }
        return $totalClaimsCount;
    }


    public function getTargetTotalWorkDurationInterval(int $TARGET_HOURS)
    {
        $targetClaimSecondsPerPerson = CarbonInterval::hours($TARGET_HOURS)->totalSeconds;
        $targetTotalWorkDurationSeconds = $targetClaimSecondsPerPerson * $this->getTotalClaimCount($this->firstWorkScheduleId, $this->lastWorkScheduleId);
        $targetTotalWorkDurationInterval = CarbonInterval::seconds($targetTotalWorkDurationSeconds);
        return $targetTotalWorkDurationInterval;
    }
    public function getTargetTotalWorkDuration(int $TARGET_HOURS)
    {
        $targetTotalWorkDurationInterval = $this->getTargetTotalWorkDurationInterval($TARGET_HOURS,  $this->firstWorkScheduleId, $this->lastWorkScheduleId);

        $targetTotalWorkDuration = TimeFormatter::convertDaysToHours($targetTotalWorkDurationInterval->cascade())->format('%H:%I:%S');

        return $targetTotalWorkDuration;
    }
    public function getRestToAchieveCompanyTarget(int $TARGET_HOURS)
    {
        $companyTotalWorkDurationInterval = $this->getCompanyTotalWorkDurationInterval($this->firstWorkScheduleId, $this->lastWorkScheduleId);
        // dump('companyTotalWorkDurationInterval', $companyTotalWorkDurationInterval);
        $targetTotalWorkDurationInterval = $this->getTargetTotalWorkDurationInterval($TARGET_HOURS,  $this->firstWorkScheduleId, $this->lastWorkScheduleId);
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
