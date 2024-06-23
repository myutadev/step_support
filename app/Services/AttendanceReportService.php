<?php

namespace App\Services;

use App\Domains\UserAttendanceRange;
use App\Domains\WholeCompanyAttendance;
use App\Repositories\AdminCommentRepository;
use App\Repositories\AdminRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\AttendanceTypeRepository;
use App\Repositories\OvertimeRepository;
use App\Repositories\RestRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AttendanceReportService
{
    protected $attendanceTypeRepository;
    protected $attendanceRepository;
    protected $restRepository;
    protected $overtimeRepository;
    protected $adminRepository;
    protected $adminCommentRepository;
    protected $workScheduleRepository;
    protected $year;
    protected $month;
    protected $userRepository;
    protected $userAttendanceRange;

    public function __construct(
        AttendanceTypeRepository $attendanceTypeRepository,
        AttendanceRepository $attendanceRepository,
        RestRepository $restRepository,
        OvertimeRepository $overtimeRepository,
        AdminRepository $adminRepository,
        AdminCommentRepository $adminCommentRepository,
        WorkScheduleRepository $workScheduleRepository,
        UserRepository $userRepository,
        UserAttendanceRange $userAttendanceRange,
    ) {
        $this->attendanceTypeRepository = $attendanceTypeRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->restRepository = $restRepository;
        $this->overtimeRepository = $overtimeRepository;
        $this->adminRepository = $adminRepository;
        $this->adminCommentRepository = $adminCommentRepository;
        $this->workScheduleRepository = $workScheduleRepository;
        $this->userRepository = $userRepository;
        $this->userAttendanceRange = $userAttendanceRange;
    }

    public function setYearAndMonth($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function getAllSchedulesForMonth()
    {
        return  $this->workScheduleRepository->getAllSchedulesForMonth($this->year, $this->month);
    }
    public function getTotalOpeningSchedules(): array
    {
        $monthlyWorkSchedule = $this->getAllSchedulesForMonth();
        $workSchedules = $monthlyWorkSchedule->filter(function ($schedule) {
            if ($schedule->specialSchedule == null) {
                return $schedule->schedule_type_id === 1;
            }
            return $schedule->specialSchedule->schedule_type_id === 1;
        });
        return $workSchedules->toArray();
    }

    public function getTotalOpeningSchedulesSoFar(): array
    {
        $today = Carbon::today();
        $currentYear = $today->year;
        $currentMonth = $today->month;
        $currentDay = $today->day;

        $monthlyWorkSchedule = $this->getAllSchedulesForMonth();

        $selectedYear = intval($monthlyWorkSchedule->first()->year);
        $selectedMonth = $monthlyWorkSchedule->first()->month;


        //当月以外の未来
        if ($selectedYear > $currentYear || ($selectedYear == $currentYear && $selectedMonth > $currentMonth)) {
            return [];
        }


        //当月以外の過去
        if ($selectedYear != $currentYear || $selectedMonth != $currentMonth) {
            return $this->getTotalOpeningSchedules();
        }

        //当月の場合
        return  array_filter($this->getTotalOpeningSchedules(), function ($workSchedule) use ($currentDay) {
            return $workSchedule['day'] <= $currentDay;
        });
    }

    public function getFirstId(): int
    {
        $monthlyWorkSchedule = $this->getAllSchedulesForMonth();
        return $monthlyWorkSchedule->first()->id;
    }
    public function getLastId(): int
    {
        $monthlyWorkSchedule = $this->getAllSchedulesForMonth();
        return $monthlyWorkSchedule->last()->id;
    }

    public function getTotalOpeningCount(): int
    {
        return count($this->getTotalOpeningSchedules());
    }

    public function getTotalOpenCountSoFar(): int
    {
        return count($this->getTotalOpeningSchedulesSoFar());
    }

    public function getActiveUsersThisMonth()
    {
        $dischargeDateCondition = Carbon::parse($this->year . "-" . $this->month);
        return $this->userRepository->getActiveUsers($dischargeDateCondition);
    }
    //1回のループで出勤時間とUserInfoの作成を同時にできるが、わかりにくくなるのであえて2回ループさせる
    public function generateUserInfoArray(): array
    {
        $userInfoArray = [];
        $activeUsers = $this->getActiveUsersThisMonth();
        $firstWorkScheduleId = $this->getFirstId();
        $lastWorkScheduleId = $this->getLastId();
        $openingSoFarThisMonth = $this->getTotalOpenCountSoFar();
        foreach ($activeUsers as $user) {
            //当月の出勤対象のAttendancesのみを抽出:出勤が遅刻or 正常
            $thisMonthAttendancesByUser = UserAttendanceRange::create($user, $firstWorkScheduleId, $lastWorkScheduleId);

            //目標時間80h 
            $restToAchieveTarget = $thisMonthAttendancesByUser->getRestToAchieveTarget(80);

            $curInfoObj = [
                'beneficiary_number' => $user->UserDetail->beneficiary_number,
                'name' => $user->getFullNameAttribute(),
                'is_on_welfare' => $user->UserDetail->is_on_welfare,
                'daysPresentSoFarThisMonth' => $thisMonthAttendancesByUser->getPresentCount(),
                'attendanceRate' => $thisMonthAttendancesByUser->getPresentRate($openingSoFarThisMonth),
                'workedHourTotalSoFarThisMonth' => $thisMonthAttendancesByUser->getFormattedTotalWorkDuration(),
                'restToAchieveTarget' => $restToAchieveTarget->invert == 1 ? "-" . $restToAchieveTarget->format('%H:%I:%S') : "" . $restToAchieveTarget->format('%H:%I:%S'),
            ];
            array_push($userInfoArray, $curInfoObj);
        }
        return $userInfoArray;
    }

    public function generateCompanyTotalWorkDurationInterval(): CarbonInterval
    {
        $activeUsers = $this->getActiveUsersThisMonth();
        $firstWorkScheduleId = $this->getFirstId();
        $lastWorkScheduleId = $this->getLastId();
        $companyTotalWorkDurationInterval = CarbonInterval::seconds(0);

        foreach ($activeUsers as $user) {
            //当月の出勤対象のAttendancesのみを抽出:出勤が遅刻or 正常
            $thisMonthAttendancesByUser = UserAttendanceRange::create($user, $firstWorkScheduleId, $lastWorkScheduleId);
            //全体の労働時間に追加
            $totalWorkDurationInterval = $thisMonthAttendancesByUser->getTotalWorkDurationInterval();
            $companyTotalWorkDurationInterval->add($totalWorkDurationInterval);
        }
        return $companyTotalWorkDurationInterval;
    }

    private function createWholeCompanyAttendance()
    {
        return  new WholeCompanyAttendance(
            $this->getActiveUsersThisMonth(),
            $this->userAttendanceRange,
            $this->getFirstId(),
            $this->getLastId(),
        );
    }

    public function generateCompanyTotalWorkDuration()
    {
        $wholeCompanyAttendance = $this->createWholeCompanyAttendance();
        return $wholeCompanyAttendance->getCompanyTotalWorkDuration();
    }

    public function generateTotalClaimsCount()
    {
        $wholeCompanyAttendance = $this->createWholeCompanyAttendance();
        return $wholeCompanyAttendance->getTotalClaimCount();
    }
    public function generateTargetTotalWorkDurationInterval($TARGET_HOURS)
    {
        $wholeCompanyAttendance = $this->createWholeCompanyAttendance();
        return $wholeCompanyAttendance->getTargetTotalWorkDurationInterval($TARGET_HOURS);
    }
    public function generateTargetTotalWorkDuration($TARGET_HOURS)
    {
        $wholeCompanyAttendance = $this->createWholeCompanyAttendance();
        return $wholeCompanyAttendance->getTargetTotalWorkDuration($TARGET_HOURS);
    }
    public function generateRestToAchieveCompanyTarget($TARGET_HOURS)
    {
        $wholeCompanyAttendance = $this->createWholeCompanyAttendance();
        return $wholeCompanyAttendance->getRestToAchieveCompanyTarget($TARGET_HOURS);
    }
    public function generateMaxTotalWorkDuration($TARGET_HOURS)
    {
        $wholeCompanyAttendance = $this->createWholeCompanyAttendance();
        return $wholeCompanyAttendance->getMaxTotalWorkDuration($TARGET_HOURS);
    }
    public function sortUserInfoArray($userInfoArray, $sortField, $sortOrder)
    {
        usort($userInfoArray, function ($a, $b) use ($sortField, $sortOrder) {
            if ($sortOrder == "asc") {
                return $a[$sortField] <=> $b[$sortField];
            } else {
                return $b[$sortField] <=> $a[$sortField];
            }
        });

        return $userInfoArray;
    }
}
