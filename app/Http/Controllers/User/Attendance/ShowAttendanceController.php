<?php

namespace App\Http\Controllers\User\Attendance;

use App\Http\Controllers\Controller;
use App\Services\UserAttendanceService;

class ShowAttendanceController extends Controller
{
    protected $userAttendanceService;

    public function __construct(UserAttendanceService $userAttendanceService)
    {
        $this->userAttendanceService = $userAttendanceService;
    }

    public function __invoke()
    {
        $attendance = $this->userAttendanceService->getUserAttendanceToday();
        $attendancesArray = $this->userAttendanceService->generateAttendanceArray($attendance);
        return view('attendances.index', compact('attendancesArray'));
    }
}
