<?php

namespace App\Http\Controllers\User\Attendance;

use App\Http\Controllers\Controller;
use App\Services\UserAttendanceService;
use Illuminate\Http\Request;

class CheckOutController extends Controller
{
    protected $userAttendanceService;

    public function __construct(UserAttendanceService $userAttendanceService)
    {
        $this->userAttendanceService = $userAttendanceService;
    }
    public function __invoke(Request $request)
    {
        $this->userAttendanceService->checkOut($request);
        return redirect()->route('attendances.index')->with(
            'requested',
            '打刻が完了しました!
        今日も一日お疲れ様でした!'
        );
    }
}
