<?php

namespace App\Http\Controllers\Admin\DailyAttendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\DailyAttendanceService;
use Illuminate\Http\Request;

class StoreAdminCommentController extends Controller
{
    protected $dailyAttendanceService;

    public function __construct(DailyAttendanceService $dailyAttendanceService)
    {
        $this->dailyAttendanceService = $dailyAttendanceService;
    }


    public function __invoke(Request $request, Attendance $attendance)
    {
        $date = $this->dailyAttendanceService->getDateByAttendance($attendance);
        $this->dailyAttendanceService->storeAdminComment($request, $attendance);

        return redirect()->route('admin.daily', compact('date'));
    }
}
