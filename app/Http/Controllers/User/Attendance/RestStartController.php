<?php

namespace App\Http\Controllers\User\Attendance;

use App\Http\Controllers\Controller;
use App\Services\UserAttendanceService;

class RestStartController extends Controller
{
    protected $userAttendanceService;

    public function __construct(UserAttendanceService $userAttendanceService)
    {
        $this->userAttendanceService = $userAttendanceService;
    }
    public function __invoke()
    {
        $this->userAttendanceService->restStart();
        return redirect()->route('attendances.index')->with('requested', "休憩開始しました");
    }
}
