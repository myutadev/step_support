<?php

namespace App\Http\Controllers\Admin\Timecard;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;


class StoreLeaveController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function __invoke(Request $request, $user_id, $sched_id)
    {
        $this->attendanceService->storeLeaveRecord($request, $user_id, $sched_id);

        return redirect()->route('admin.timecard', ['yearmonth' => $request->yearmonth, 'id' => $user_id]);
    }
}
