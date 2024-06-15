<?php

namespace App\Services;

use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class WorkScheduleService
{
    protected $workScheduleRepository;

    public function __construct(WorkScheduleRepository $workScheduleRepository)
    {
        $this->workScheduleRepository = $workScheduleRepository;
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
}
