<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class WorkScheduleService
{
    protected $workScheduleRepository;
    protected $monthUserSelectorService;
    protected $adminRepository;


    public function __construct(WorkScheduleRepository $workScheduleRepository, MonthUserSelectorService $monthUserSelectorService, AdminRepository $adminRepository)
    {
        $this->workScheduleRepository = $workScheduleRepository;
        $this->monthUserSelectorService = $monthUserSelectorService;
        $this->adminRepository = $adminRepository;
    }

    public function getWorkDayName(): string
    {
        return $this->workScheduleRepository->getWorkDayName();
    }

    public function getSelectedMonthWorkSchedulesByUser(int $year, int $month, int $user_id): Collection
    {
        return $this->workScheduleRepository->getSelectedMonthWorkSchedulesByUser($year, $month, $user_id);
    }

    public function getAllSchedulesForMonth(int $year, int $month): Collection
    {
        return $this->workScheduleRepository->getAllSchedulesForMonth($year, $month);
    }

    public function getTotalOpeningSchedule(collection $schedules): array
    {
        $workSchedules = $schedules->filter(function ($schedule) {

            return $schedule->schedule_type_id === 1;
        });
        return $workSchedules->toArray();
    }

    public function getTotalOpeningScheduleSoFar(int $year, int $month, array $totalWorkSchedules): array
    {
        //当月の出勤日なら本日まで、それ以外はそのままの開所日を返す
        if ($year == Carbon::today()->year && $month == Carbon::today()->month) {
            $baseDate = Carbon::today()->day;

            return  array_filter($totalWorkSchedules, function ($workSchedule) use ($baseDate) {
                return $workSchedule['day'] <= $baseDate;
            });
        } else {
            return $totalWorkSchedules;
        };
    }

    public function generateIndexWrokscheduleData($year, $month)
    {
        $monthlyWorkScheduleData = [];

        $thisMonthWorkschedules = $this->getAllSchedulesForMonth($year, $month);

        foreach ($thisMonthWorkschedules as $workSchedule) {
            $curScheduleType = $workSchedule->scheduleType;
            $curSpecialSchedule = $workSchedule->specialSchedule;
            $curCarbonDate = Carbon::parse($workSchedule->date);
            $curDay = $curCarbonDate->isoFormat('ddd');

            if (!$curSpecialSchedule) {
                $curScheduleObj = [
                    'id' => $workSchedule->id,
                    'special_sched_id' => "",
                    'date' => $workSchedule->date,
                    'day' => $curDay,
                    'scheduleType' => $curScheduleType->name,
                    'description' => "",
                ];
                array_push($monthlyWorkScheduleData, $curScheduleObj);
            } else {
                $overwriteScheduleType = $curSpecialSchedule->schedule_type;
                // dd($overwriteScheduleType);
                $curScheduleObj = [
                    'id' => $workSchedule->id,
                    'special_sched_id' => $curSpecialSchedule->id,
                    'date' => $workSchedule->date,
                    'day' => $curDay,
                    'scheduleType' => $overwriteScheduleType->name,
                    'description' => $curSpecialSchedule->description,
                ];
                array_push($monthlyWorkScheduleData, $curScheduleObj);
            }
        }

        return $monthlyWorkScheduleData;
    }
}
