<?php

namespace App\Http\Controllers\Admin\Workschedule;

use App\Http\Controllers\Controller;
use App\Services\MonthUserSelectorService;
use App\Services\WorkScheduleService;
use Illuminate\Http\Request;

class CreateWorkscheduleController extends Controller
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
        $targetWorkSchedule  = $this->workScheduleService->generateCreateWorkscheduleData($request);
        $scheduleTypes = $this->workScheduleService->getAllScheduleType();
        return view('admin.attendances.workschedulecreate', compact('targetWorkSchedule', 'scheduleTypes'));
    }
}
