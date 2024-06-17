<?php

namespace App\Http\Controllers\Admin\Timecard;

use App\Http\Controllers\Controller;

use App\Services\AttendanceService;
use App\Services\MonthUserSelectorService;
use App\Services\TimeCardService;
use App\Services\WorkScheduleService;

class IndexTimecardController extends Controller
{

    protected $monthUserSelectorService;
    protected $workScheduleService;
    protected $attendanceService;
    protected $timeCardService;

    public function __construct(
        MonthUserSelectorService $monthUserSelectorService,
        WorkScheduleService $workScheduleService,
        AttendanceService $attendanceService,
        TimeCardService $timeCardService
    ) {
        $this->monthUserSelectorService = $monthUserSelectorService;
        $this->workScheduleService = $workScheduleService;
        $this->attendanceService = $attendanceService;
        $this->timeCardService = $timeCardService;
    }

    public function __invoke($yearmonth = null, $user_id = null)
    {
        //編集ボタンと欠勤ボタンの出し分けに使う
        $workDayName = $this->workScheduleService->getWorkDayName();

        //欠勤データ登録モーダルで表示される出欠種別の項目を作成
        $leaveTypes = $this->attendanceService->getLeaveTypes();

        //monthUserSelectorDataObj has 'users','year','month','user_id'
        $monthUserSelectorDataObj = $this->monthUserSelectorService->createMonthUserSelectorDataObj($yearmonth, $user_id);

        // テーブル内の表示データの作成
        $monthlyAttendanceData = $this->timeCardService->generateMonthlyAttendanceData($monthUserSelectorDataObj['year'], $monthUserSelectorDataObj['month'], $monthUserSelectorDataObj['user_id']);

        return view(
            'admin.attendances.admintimecard',
            compact('monthlyAttendanceData', 'monthUserSelectorDataObj', 'leaveTypes', 'workDayName')
        );
    }
}
