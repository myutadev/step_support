<?php

namespace App\Http\Controllers\Admin\DailyAttendance;

use App\Http\Controllers\Controller;
use App\Models\AdminComment;
use App\Services\DailyAttendanceService;
use Illuminate\Http\Request;

class UpdateAdminCommentController extends Controller
{
    protected $dailyAttendanceService;

    public function __construct(DailyAttendanceService $dailyAttendanceService)
    {
        $this->dailyAttendanceService = $dailyAttendanceService;
    }



    public function __invoke(Request $request, AdminComment $admincomment)
    {
        $date = $this->dailyAttendanceService->getDateByAdminComment($admincomment);
        $this->dailyAttendanceService->updateAdminComment($request, $admincomment);
        return redirect()->route('admin.daily', compact('date'));
    }
}
