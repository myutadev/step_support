<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Domains\UserAttendanceRange;
use App\Services\AttendanceReportService;
use App\Services\MonthUserSelectorService;
use Illuminate\Http\Request;




class ShowReportController extends Controller
{
    protected $userAttendanceRange;
    protected $attendanceReportService;
    protected $monthUserSelectorService;


    public function __construct(
        UserAttendanceRange $userAttendanceRange,
        AttendanceReportService $attendanceReportService,
        MonthUserSelectorService $monthUserSelectorService,

    ) {

        $this->userAttendanceRange = $userAttendanceRange;
        $this->attendanceReportService = $attendanceReportService;
        $this->monthUserSelectorService = $monthUserSelectorService;
    }


    public function __invoke(Request $request)
    {
        $TARGET_HOURS = 4;
        $sortField = $request->input('sortField', 'name');
        $sortOrder = $request->input('sortOrder', 'asc');
        $yearmonth = $request->input('yearmonth');


        //当月の開所日データと、目標勤務時間の作成
        $selectedYearMonth = $this->monthUserSelectorService->getSelectedYearMonth($yearmonth);
        $year = $selectedYearMonth['year'];
        $month = $selectedYearMonth['month'];


        //レポート用Atttendanceのインスタンス作成
        $this->attendanceReportService->setYearAndMonth($year, $month);

        //対象月の開所日と本日までの開所日日数を取得
        $totalOpeningSchedules = $this->attendanceReportService->getTotalOpeningSchedules();
        $totalOpeningSchedulesSoFar = $this->attendanceReportService->getTotalOpenCountSoFar();
        $openingSoFarThisMonth = $this->attendanceReportService->getTotalOpenCountSoFar();
        $totalOpeningThisMonth =  $this->attendanceReportService->getTotalOpeningCount();
        $firstWorkScheduleId =  $this->attendanceReportService->getFirstId();
        $lastWorkScheduleId =  $this->attendanceReportService->getLastId();


        //1.アクティブなユーザーを全て取得 - 退所日が前月以前の人
        $activeUsers = $this->attendanceReportService->getActiveUsersThisMonth();

        $userInfoArray = $this->attendanceReportService->generateUserInfoArray();
        $sorteUserInfoArray  = $this->attendanceReportService->sortUserInfoArray($userInfoArray, $sortField, $sortOrder);

        $companyTotalWorkDurationInterval = $this->attendanceReportService->generateCompanyTotalWorkDurationInterval();


        //事業所全体の目標請求時間の算出 1人あたり4時間
        $companyTotalWorkDuration = $this->attendanceReportService->generateCompanyTotalWorkDuration();
        $totalClaimsCount = $this->attendanceReportService->generateTotalClaimsCount();
        $targetTotalWorkDurationInterval = $this->attendanceReportService->generateTargetTotalWorkDurationInterval($TARGET_HOURS);
        $targetTotalWorkDuration = $this->attendanceReportService->generateTargetTotalWorkDuration($TARGET_HOURS);
        $restToAchieveCompanyTarget = $this->attendanceReportService->generateRestToAchieveCompanyTarget($TARGET_HOURS);
        //月次勤務時間の上限
        $maxTotalWorkDuration = $this->attendanceReportService->generateMaxTotalWorkDuration($openingSoFarThisMonth);


        return view(
            'admin.attendances.report',
            compact(
                //from request
                'sortField',
                'sortOrder',
                'year',
                'month',
                'sorteUserInfoArray',
                'openingSoFarThisMonth',
                'totalOpeningThisMonth',
                'totalClaimsCount',
                'companyTotalWorkDuration',
                'targetTotalWorkDuration',
                'maxTotalWorkDuration',
                'restToAchieveCompanyTarget'
            )
        );
    }
}
