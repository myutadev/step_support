<?php

namespace App\Http\Controllers\User\Timecard;

use App\Http\Controllers\Controller;
use App\Services\MonthUserSelectorService;
use App\Services\UserTimecardService;
use Illuminate\Support\Facades\Auth;

class ShowTimecardController extends Controller
{
    protected $userTimecardService;
    protected $monthUserSelectorService;

    public function __construct(
        UserTimecardService $userTimecardService,
        MonthUserSelectorService $monthUserSelectorService,
    ) {
        $this->userTimecardService = $userTimecardService;
        $this->monthUserSelectorService = $monthUserSelectorService;
    }

    public function __invoke($yearmonth = null)
    {
        $user = Auth::user();
        $yearmonthObj = $this->monthUserSelectorService->getSelectedYearMonth($yearmonth);
        $monthlyAttendanceData = $this->userTimecardService->generateMonthlyAttendanceData($yearmonth, $user);
        return view('attendances.timecard', compact('monthlyAttendanceData', 'yearmonthObj'));
    }
}
