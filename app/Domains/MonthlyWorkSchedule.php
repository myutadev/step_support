<?php

namespace App\Domains;

use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class MonthlyWorkSchedule
{
    protected $monthlyWorkSchedule;

    public function __construct(Collection $monthlyWorkSchedule)
    {
        $this->monthlyWorkSchedule = $monthlyWorkSchedule;
    }

    public static function create(int $year, int $month)
    {

        $thisMonthAllSchedules =
            WorkSchedule::with(['scheduleType', 'specialSchedule'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();


        return new self($thisMonthAllSchedules);
    }

    public function getTotalOpeningSchedules(): array
    {
        $workSchedules = $this->monthlyWorkSchedule->filter(function ($schedule) {
            return $schedule->schedule_type_id === 1;
        });
        return $workSchedules->toArray();
    }

    public function getTotalOpeningSchedulesSoFar(): array
    {
        $today = Carbon::today();
        $currentYear = $today->year;
        $currentMonth = $today->month;
        $currentDay = $today->day;

        $selectedYear = intval($this->monthlyWorkSchedule->first()->year);
        $selectedMonth = $this->monthlyWorkSchedule->first()->month;


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
        return $this->monthlyWorkSchedule->first()->id;
    }
    public function getLastId(): int
    {
        return $this->monthlyWorkSchedule->last()->id;
    }

    public function getTotalOpeningCount(): int
    {
        return count($this->getTotalOpeningSchedules());
    }

    public function getTotalOpenCountSoFar(): int
    {
        return count($this->getTotalOpeningSchedulesSoFar());
    }
}
