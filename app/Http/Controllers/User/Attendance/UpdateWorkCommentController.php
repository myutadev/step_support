<?php

namespace App\Http\Controllers\User\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\UserAttendanceService;
use Illuminate\Http\Request;

class UpdateWorkCommentController extends Controller
{
    protected $userAttendanceService;

    public function __construct(UserAttendanceService $userAttendanceService)
    {
        $this->userAttendanceService = $userAttendanceService;
    }
    public function __invoke(Request $request, Attendance $attendance)
    {
        $this->userAttendanceService->updateWorkComment($request, $attendance);
        return redirect()->action(ShowAttendanceController::class);
    }
}
