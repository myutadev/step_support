<?php

namespace App\Http\Controllers;

use App\Domains\monthlyWorkSchedule;
use App\Domains\UserAttendanceRange;
use App\Domains\WholeCompanyAttendance;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WorkSchedule;
use App\Services\AttendanceService;
use App\Services\UserService;
use App\Services\WorkScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkTimeService;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;

class AdminReportController extends Controller
{

    protected $userAttendanceRange;



    public function __construct(
        UserAttendanceRange $userAttendanceRange,
    ) {

        $this->userAttendanceRange = $userAttendanceRange;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $TARGET_HOURS = 4;

        $sortField = $request->input('sortField', 'name');
        $sortOrder = $request->input('sortOrder', 'asc');


        $yearmonth = $request->input('yearmonth');


        //当月の開所日データと、目標勤務時間の作成
        if ($yearmonth == null) {
            $today = Carbon::today();
            $year = $today->year;
            $month = sprintf("%02d", $today->month);
        } else {
            $yearMonthArr = explode("-", $yearmonth);
            $year = $yearMonthArr[0];
            $month = sprintf("%02d", $yearMonthArr[1]);
        }

        $dischargeDateCondition = Carbon::parse($year . "-" . $month);

        //1.アクティブなユーザーを全て取得 - 退所日が前月以前の人
        $activeUsers = User::getActiveUsers($dischargeDateCondition);

        //対象月の開所日と本日までの開所日日数を取得
        $thisMonthWorkSchedules = MonthlyWorkSchedule::create($year, $month);
        $totalOpeningSchedules = $thisMonthWorkSchedules->getTotalOpeningSchedules();
        $totalOpeningSchedulesSoFar = $thisMonthWorkSchedules->getTotalOpenCountSoFar();
        $openingSoFarThisMonth = $thisMonthWorkSchedules->getTotalOpenCountSoFar();
        $totalOpeningThisMonth = $thisMonthWorkSchedules->getTotalOpeningCount();
        $firstWorkScheduleId =  $thisMonthWorkSchedules->getFirstId();
        $lastWorkScheduleId =  $thisMonthWorkSchedules->getLastId();

        $userInfoArray = [];

        //請求人数累計カウント用 + 合計実勤務時間計算用
        $companyTotalWorkDurationInterval = CarbonInterval::seconds(0);

        //3.各ユーザーごとに繰り返し処理をして各データを作成
        foreach ($activeUsers as $user) {
            //当月の出勤対象のAttendancesのみを抽出:出勤が遅刻or 正常
            $thisMonthAttendancesByUser = UserAttendanceRange::create($user, $firstWorkScheduleId, $lastWorkScheduleId);

            //目標時間80h 
            $restToAchieveTarget = $thisMonthAttendancesByUser->getRestToAchieveTarget(80);

            //全体の労働時間に追加
            $totalWorkDurationInterval = $thisMonthAttendancesByUser->getTotalWorkDurationInterval();
            $companyTotalWorkDurationInterval->add($totalWorkDurationInterval);

            $curInfoObj = [
                'beneficiary_number' => $user->UserDetail->beneficiary_number,
                'name' => $user->getFullNameAttribute(),
                'is_on_welfare' => $user->UserDetail->is_on_welfare,
                'daysPresentSoFarThisMonth' => $thisMonthAttendancesByUser->getPresentCount(),
                'attendanceRate' => $thisMonthAttendancesByUser->getPresentRate($openingSoFarThisMonth),
                'workedHourTotalSoFarThisMonth' => $thisMonthAttendancesByUser->getFormattedTotalWorkDuration(),
                'restToAchieveTarget' => $restToAchieveTarget->invert == 1 ? "-" . $restToAchieveTarget->format('%H:%I:%S') : "" . $restToAchieveTarget->format('%H:%I:%S'),
            ];
            array_push($userInfoArray, $curInfoObj);
        }

        //事業所全体の目標請求時間の算出 1人あたり4時間
        $wholeCompanyAttendance = new WholeCompanyAttendance($activeUsers, $this->userAttendanceRange);
        $companyTotalWorkDuration = $wholeCompanyAttendance->getCompanyTotalWorkDuration($firstWorkScheduleId, $lastWorkScheduleId);
        $totalClaimsCount = $wholeCompanyAttendance->getTotalClaimCount($firstWorkScheduleId, $lastWorkScheduleId);


        $targetTotalWorkDurationInterval = $wholeCompanyAttendance->getTargetTotalWorkDurationInterval($TARGET_HOURS, $firstWorkScheduleId, $lastWorkScheduleId);
        $targetTotalWorkDuration = $wholeCompanyAttendance->getTargetTotalWorkDuration($TARGET_HOURS, $firstWorkScheduleId, $lastWorkScheduleId);
        $restToAchieveCompanyTarget = $wholeCompanyAttendance->getRestToAchieveCompanyTarget($TARGET_HOURS, $firstWorkScheduleId, $lastWorkScheduleId);

        $restToAchieveCompanyTargetInterval = $companyTotalWorkDurationInterval->sub($targetTotalWorkDurationInterval)->cascade();

        //月次勤務時間の上限
        $maxTotalWorkDuration = $wholeCompanyAttendance->getMaxTotalWorkDuration($openingSoFarThisMonth);

        //ソートの実装
        usort($userInfoArray, function ($a, $b) use ($sortField, $sortOrder) {

            if ($sortOrder == "asc") {
                return $a[$sortField] <=> $b[$sortField];
            } else {
                return $b[$sortField] <=> $a[$sortField];
            }
        });



        return view(
            'admin.attendances.report',
            compact(
                'userInfoArray',
                'sortField',
                'sortOrder',
                'year',
                'month',
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
