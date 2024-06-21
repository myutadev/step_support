<?php

namespace App\Http\Controllers\Admin\Workschedule;

use App\Http\Controllers\Controller;
use App\Services\MonthUserSelectorService;
use App\Services\WorkScheduleService;

class IndexWorkscheduleController extends Controller
{
    protected $workScheduleService;
    protected $monthUserSelectorService;

    public function __construct(
        WorkScheduleService $workScheduleService,
        MonthUserSelectorService $monthUserSelectorService
    ) {
        $this->workScheduleService = $workScheduleService;
        $this->monthUserSelectorService = $monthUserSelectorService;
    }
    public function __invoke($yearmonth = null)
    {
        $selectedYearMonth = $this->monthUserSelectorService->getSelectedYearMonth($yearmonth);
        $year = $selectedYearMonth['year'];
        $month = $selectedYearMonth['month'];
        $monthlyWorkScheduleData = $this->workScheduleService->generateIndexWrokscheduleData($year, $month);

        return view('admin.attendances.workschedule', compact('monthlyWorkScheduleData', 'year', 'month'));
    }
}
