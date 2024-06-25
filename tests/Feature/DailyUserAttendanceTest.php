<?php

namespace Tests\Feature;

use App\Domains\Attendance\DailyAdminComment;
use App\Domains\Attendance\DailyOvertime;
use App\Domains\Attendance\DailyRest;
use App\Domains\Attendance\DailyTimeSlot;
use App\Domains\Attendance\DailyUserAttendance;
use App\Models\Admin;
use App\Models\AdminComment;
use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\ScheduleType;
use App\Models\SpecialSchedule;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

use function PHPUnit\Framework\assertEquals;

class DailyUserAttendanceTest extends TestCase
{

    protected $userDetailMock;
    protected $userMock;
    protected $attendanceMock;
    protected $dailyOvertimeMock;
    protected $dailyRestMock;
    protected $dailyTimeSlotMockForRest;
    protected $dailyTimeSlotMockForOvertime;
    protected $dailyAdminCommentMock;
    protected $dailyUserAttendance;
    protected $workScheduleMock;
    protected $specialScheduleMock;
    protected $scheduleTypeOpenMock;
    protected $scheduleTypeCloseMock;
    protected $attendanceTypeMock;
    protected $adminCommentMock;
    protected $adminMock;

    public function setUp(): void
    {
        parent::setUp();
        //ユーザーのモックをセットアップ
        $this->userDetailMock = Mockery::mock(UserDetail::class);
        $this->userDetailMock->shouldReceive('getAttribute')
            ->with('beneficiary_number')
            ->andReturn('0000029918');

        $this->userMock = Mockery::mock(User::class);
        $this->userMock->shouldReceive('getAttribute')
            ->with('userDetail')
            ->andReturn($this->userDetailMock);
        $this->userMock->shouldReceive('getFullNameAttribute')
            ->andReturn('松原 勇太');


        //Attendanceのモックをセットアップ
        $this->attendanceTypeMock = Mockery::mock(AttendanceType::class);
        $this->attendanceTypeMock->shouldReceive('getAttribute')->with('name')->andReturn('通常勤務');

        $this->attendanceMock = Mockery::mock(Attendance::class);
        $this->attendanceMock->shouldReceive('getAttribute')
            ->with('user')
            ->andReturn($this->userMock);

        $this->attendanceMock->shouldReceive('getAttribute')
            ->with('attendanceType')
            ->andReturn($this->attendanceTypeMock);

        $this->attendanceMock->shouldReceive('check_in_time')
            ->andReturn('11:00:00');
        $this->attendanceMock->shouldReceive('body_temp')
            ->andReturn(36.2);
        $this->attendanceMock->shouldReceive('id')
            ->andReturn(2);
        $this->attendanceMock->shouldReceive('check_out_time')
            ->andReturn('14:00:00');
        $this->attendanceMock->shouldReceive('is_overtime')
            ->andReturn(0);
        $this->attendanceMock->shouldReceive('work_description')
            ->andReturn('インスタ');
        $this->attendanceMock->shouldReceive('work_comment')
            ->andReturn('楽しい');

        //残業のモックをセットアップ
        $this->dailyTimeSlotMockForOvertime = Mockery::mock(DailyTimeSlot::class);
        $this->dailyTimeSlotMockForOvertime->shouldReceive('sumTotalDuration')->andReturn(CarbonInterval::minute(45));
        $this->dailyTimeSlotMockForOvertime->shouldReceive('showAllTimeSlotsStr')->andReturn("15:00-15:45");

        $this->dailyOvertimeMock = Mockery::mock(DailyOvertime::class);
        $this->dailyOvertimeMock->shouldReceive('getDailyTimeSlot')->andReturn($this->dailyTimeSlotMockForOvertime);

        //休憩のモックをセットアップ

        $this->dailyTimeSlotMockForRest = Mockery::mock(DailyTimeSlot::class);
        $this->dailyTimeSlotMockForRest->shouldReceive('sumTotalDuration')->andReturn(CarbonInterval::hour(1));
        $this->dailyTimeSlotMockForRest->shouldReceive('showAllTimeSlotsStr')->andReturn("12:00-13:00");

        $this->dailyRestMock = Mockery::mock(DailyRest::class);
        $this->dailyRestMock->shouldReceive('getDailyTimeSlot')->andReturn($this->dailyTimeSlotMockForRest);

        //adminのモックをセットアップ
        $this->adminMock = Mockery::mock(Admin::class);
        $this->adminMock->shouldReceive('getAttribute')->with('full_name')
            ->andReturn('花村 そら');

        // DailyAdminCommentのセットアップ

        $this->adminCommentMock = Mockery::mock(AdminComment::class);
        $this->adminCommentMock->shouldReceive('getAttribute')
            ->with('admin_description')
            ->andReturn('インスタに取り組みました');
        $this->adminCommentMock->shouldReceive('getAttribute')
            ->with('admin_comment')
            ->andReturn('頑張ってました');
        $this->adminCommentMock->shouldReceive('getAttribute')
            ->with('admin')
            ->andReturn($this->adminMock);

        $this->dailyAdminCommentMock = Mockery::mock(DailyAdminComment::class)->makePartial();
        $this->dailyAdminCommentMock->push($this->adminCommentMock);

        //ワークスケジュールのモックをセットアップ
        $this->scheduleTypeOpenMock = Mockery::mock(ScheduleType::class);
        $this->scheduleTypeOpenMock->shouldReceive('getAttribute')->with('name')->andReturn('開所日');
        $this->scheduleTypeCloseMock = Mockery::mock(ScheduleType::class);
        $this->scheduleTypeCloseMock->shouldReceive('getAttribute')->with('name')->andReturn('所定休日');

        $this->specialScheduleMock = Mockery::mock(SpecialSchedule::class);
        $this->specialScheduleMock->shouldReceive('getAttribute')->with('scheduleType')->andReturn($this->scheduleTypeOpenMock);
        $this->workScheduleMock  = Mockery::mock(WorkSchedule::class);
        $this->workScheduleMock->shouldReceive('getAttribute')->with('date')->andReturn(Carbon::create(2024, 5, 2));
        $this->workScheduleMock->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $this->workScheduleMock->shouldReceive('scheduleType')->with('name')->andReturn('所定休日');
        // $this->workScheduleMock->shouldReceive('specialSchedule->scheduleType->name')->andReturn('所定休日');
        $this->workScheduleMock->shouldReceive('getAttribute')->with('specialSchedule')->andReturn($this->specialScheduleMock);


        $this->dailyUserAttendance = new DailyUserAttendance(
            $this->attendanceMock,
            $this->workScheduleMock,
            $this->dailyOvertimeMock,
            $this->dailyRestMock,
            $this->dailyAdminCommentMock
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }


    public function testGetAttendanceType(): void
    {
        assertEquals('通常勤務', $this->dailyUserAttendance->getAttendanceType());
    }

    public function testGetWorkScheduleId(): void
    {
        assertEquals(3, $this->dailyUserAttendance->getWorkScheduleId());
    }
    public function testGetDate(): void
    {
        assertEquals(Carbon::create('2024-05-02'), $this->dailyUserAttendance->getDate());
    }
    public function testGetScheduleType(): void
    {
        assertEquals('開所日', $this->dailyUserAttendance->getScheduleType());
    }

    public function testGetCheckIn(): void
    {
        assertEquals('11:00:00', $this->dailyUserAttendance->getCheckIn());
    }


    public function testGetCheckOut(): void
    {
        assertEquals('14:00:00', $this->dailyUserAttendance->getCheckOut());
    }
    public function testGetBodyTemp(): void
    {
        assertEquals(36.2, $this->dailyUserAttendance->getBodyTemp());
    }
    public function testGetIsOvertime(): void
    {
        assertEquals("無", $this->dailyUserAttendance->getIsOvertime());
    }
    public function testGetDescription(): void
    {
        assertEquals("インスタ", $this->dailyUserAttendance->getDescription());
    }
    public function testGetComment(): void
    {
        assertEquals("楽しい", $this->dailyUserAttendance->getComment());
    }
    public function testGetFullName(): void
    {
        assertEquals("松原 勇太", $this->dailyUserAttendance->getFullName());
    }

    public function testGetBeneficiaryNumber(): void
    {
        assertEquals('0000029918', $this->dailyUserAttendance->getBeneficiaryNumber());
    }

    public function testShowTotalBaseWorkingDuration(): void
    {
        $expectedResult = CarbonInterval::hours(3);
        assertEquals($expectedResult, $this->dailyUserAttendance->showTotalBaseWorkingDuration());
    }

    public function testGetDuration(): void
    {
        $dailyUserAttendance = $this->dailyUserAttendance;
        $dailyRest = $dailyUserAttendance->getDailyRest();
        assertEquals(CarbonInterval::hour(1), $dailyUserAttendance->getDuration($dailyRest->getDailyTimeSlot()));

        $dailyOvertime = $dailyUserAttendance->getDailyOvertime();
        assertEquals(CarbonInterval::minute(45), $dailyUserAttendance->getDuration($dailyOvertime->getDailyTimeSlot()));
    }


    public function testShowNetWorkDuration(): void
    {
        // 11-14, rest: 12-13 overtime 45 min 
        $expectedResult = CarbonInterval::hours(2)->minutes(45);
        assertEquals($expectedResult, $this->dailyUserAttendance->showNetWorkDuration());
    }
    public function testShowAllDailyRestStr(): void
    {
        $expectedResult = "12:00-13:00";
        assertEquals($expectedResult, $this->dailyUserAttendance->showAllDailyRestStr());
    }
    public function testShowAllDailyOvertimeStr(): void
    {
        $expectedResult = "15:00-15:45";
        assertEquals($expectedResult, $this->dailyUserAttendance->showAllDailyOvertimeStr());
    }
    public function testShowAllDailyAdminCommentStr(): void
    {
        $expectedResult = "インスタに取り組みました :頑張ってました (花村 そら)";
        // dd($this->dailyUserAttendance->showAllDailyAdminCommentStr());
        assertEquals($expectedResult, $this->dailyUserAttendance->showAllDailyAdminCommentStr());
    }


    // public function testCreateAttendanceObj(): void
    // {
    //     $exptectedObj = [
    //         'attendance_id' => 2,
    //         'attendance_type' => ,
    //         'workSchedule_id' => $this->getWorkScheduleId(),
    //         'date' => $this->getDate(),
    //         'scheduleType' => $this->getScheduleType(),
    //         'bodyTemp' => $this->getBodyTemp(),
    //         'checkin' => $this->getCheckIn(),
    //         'checkout' => $this->getCheckOut(),
    //         'is_overtime' => $this->getIsOvertime(),
    //         'rest' => $this->getDailyRest()->getDailyTimeSlot()->showAllTimeSlotsStr(),
    //         'overtime' => $this->getDailyOvertime()->getDailyTimeSlot()->showAllTimeSlotsStr(),
    //         'duration' => TimeFormatter::carbonIntervalToStringHours($this->showNetWorkDuration()),
    //         'workDescription' => $this->getDescription(),
    //         'workComment' => $this->getComment(),
    //         'admin_comment' => $this->getDailyAdminComment(),

    //     ];

    //     assertEquals($exptectedObj, $this->dailyUserAttendance->createAttendanceObj());
    // }
}
