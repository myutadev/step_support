<?php

namespace App\Http\Controllers\Admin\DailyAttendance;

use App\Http\Controllers\Controller;
use App\Services\DailyAttendanceService;

class IndexDailyAttendanceController extends Controller
{

    protected $dailyAttendanceService;

    public function __construct(DailyAttendanceService $dailyAttendanceService)
    {
        $this->dailyAttendanceService = $dailyAttendanceService;
    }

    public function __invoke($date = null)
    {
        $this->dailyAttendanceService->setDate($date);
        $selectedDate = $this->dailyAttendanceService->generateSelectedDate();
        $dailyAttendanceData = $this->dailyAttendanceService->generateDailyAttenanceData();
        return view('admin.attendances.daily', compact('dailyAttendanceData', 'selectedDate'));
    }
}
