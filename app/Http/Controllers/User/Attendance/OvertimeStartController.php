<?php

namespace App\Http\Controllers\User\Attendance;

use App\Http\Controllers\Controller;
use App\Services\UserAttendanceService;

class OvertimeStartController extends Controller
{
    protected $userAttendanceService;

    public function __construct(UserAttendanceService $userAttendanceService)
    {
        $this->userAttendanceService = $userAttendanceService;
    }
    public function __invoke()
    {
        $this->userAttendanceService->overtimeStart();
        return redirect()->route('attendances.index')->with('requested', "残業開始しました");
    }
}
