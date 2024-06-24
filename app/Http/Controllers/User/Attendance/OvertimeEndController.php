<?php

namespace App\Http\Controllers\User\Attendance;

use App\Http\Controllers\Controller;
use App\Services\UserAttendanceService;

class OvertimeEndController extends Controller
{
    protected $userAttendanceService;

    public function __construct(UserAttendanceService $userAttendanceService)
    {
        $this->userAttendanceService = $userAttendanceService;
    }
    public function __invoke()
    {
        $this->userAttendanceService->overtimeEnd();
        return redirect()->route('attendances.index')->with('requested', "残業終了しました");
    }
}
