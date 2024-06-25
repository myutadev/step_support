<?php

namespace App\Http\Controllers\Admin\Workschedule;

use App\Http\Controllers\Controller;
use App\Services\MonthUserSelectorService;
use App\Services\WorkScheduleService;
use Illuminate\Http\Request;

class StoreWorkscheduleController extends Controller
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

    public function __invoke(Request $request)
    {
        $yearmonth = $this->monthUserSelectorService->generateSelectedYearMonthByWorkSchedId($request->workSchedule_id);
        $this->workScheduleService->storeSpecialSchedule($request);
        return redirect()->route('admin.workschedules', compact('yearmonth'));
    }
}
