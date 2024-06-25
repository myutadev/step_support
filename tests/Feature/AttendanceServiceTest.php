<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Company;
use App\Models\Counselor;
use App\Models\DisabilityCategory;
use App\Models\Residence;
use App\Models\ScheduleType;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WorkSchedule;
use App\Services\AttendanceService;
use Database\Seeders\AttendanceTypeSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CounselorSeeder;
use Database\Seeders\DisabilityCategorySeeder;
use Database\Seeders\ResidenceSeeder;
use Database\Seeders\ScheduleTypeSeeder;
use Database\Seeders\UserDetailSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    // protected static $db_inited = false;
    use RefreshDatabase;
    protected $user;
    protected $attendances;
    protected $attendanceService;

    public function testGetPresentAttendance_filterOnlyPresentData(): void
    {
        //arrange
        $this->seed(CompanySeeder::class);
        $this->seed(CounselorSeeder::class);
        $this->seed(DisabilityCategorySeeder::class);
        $this->seed(ResidenceSeeder::class);
        $this->seed(UserSeeder::class);
        $this->user = User::first();
        $companyId = Company::first()->id;
        $disabilityCategoryId = DisabilityCategory::first()->id;
        $counselorId = Counselor::first()->id;
        $residenceId = Residence::first()->id;


        UserDetail::factory()->create([
            'user_id' => $this->user->id,
            'disability_category_id' => $disabilityCategoryId, // 必要な外部キーを設定
            'counselor_id' => $counselorId,
            'residence_id' => $residenceId,
            'company_id' => $companyId,
        ]);

        $this->seed(ScheduleTypeSeeder::class);
        $this->seed(AttendanceTypeSeeder::class);
        $this->attendanceService = new AttendanceService;
        $this->attendances = new Collection();

        for ($i = 1; $i <= 6; $i++) {

            // WorkScheduleと関連付けられたAttendanceを作成
            WorkSchedule::factory()->create([
                'id' => $i, // 明示的にIDを設定
                'schedule_type_id' => ScheduleType::factory()->create()->id,
            ]);

            // 6個中2個を通常勤務、4つを欠勤
            if ($i > 2) {
                $this->attendances->push(Attendance::factory()->create([
                    'work_schedule_id' => $i,
                    'attendance_type_id' => 3, // 欠勤
                    'user_id' => $this->user->id,
                    'company_id' => $companyId,
                ]));
            } else {
                $this->attendances->push(Attendance::factory()->create([
                    'work_schedule_id' => $i,
                    'attendance_type_id' => 1, // 通常勤務
                    'user_id' => $this->user->id,
                    'company_id' => $companyId,
                ]));
            }
        };

        //act
        $result =  count($this->attendanceService->getPresentAttendance($this->attendances));
        //assert
        $expected = 2;
        $this->assertEquals($expected, $result);
    }



    public function testGetAttendanceRange_FiltersAttendancesCorrenctly(): void
    {
        //arrange
        $this->seed(CompanySeeder::class);
        $this->seed(CounselorSeeder::class);
        $this->seed(DisabilityCategorySeeder::class);
        $this->seed(ResidenceSeeder::class);
        $this->seed(UserSeeder::class);
        $this->user = User::first();
        $companyId = Company::first()->id;
        $disabilityCategoryId = DisabilityCategory::first()->id;
        $counselorId = Counselor::first()->id;
        $residenceId = Residence::first()->id;


        UserDetail::factory()->create([
            'user_id' => $this->user->id,
            'disability_category_id' => $disabilityCategoryId, // 必要な外部キーを設定
            'counselor_id' => $counselorId,
            'residence_id' => $residenceId,
            'company_id' => $companyId,
        ]);

        $this->seed(ScheduleTypeSeeder::class);
        $this->seed(AttendanceTypeSeeder::class);



        $this->attendanceService = new AttendanceService;
        $this->attendances = new Collection();

        for ($i = 1; $i <= 6; $i++) {

            // WorkScheduleと関連付けられたAttendanceを作成
            WorkSchedule::factory()->create([
                'id' => $i, // 明示的にIDを設定
                'schedule_type_id' => ScheduleType::factory()->create()->id,
            ]);

            // 6個中2個を通常勤務、4つを欠勤
            if ($i > 2) {

                $this->attendances->push(Attendance::factory()->create([
                    'work_schedule_id' => $i,
                    'attendance_type_id' => 3, // 欠勤
                    'user_id' => $this->user->id,
                    'company_id' => $companyId,
                ]));
            } else {
                $this->attendances->push(Attendance::factory()->create([
                    'work_schedule_id' => $i,
                    'attendance_type_id' => 1, // 通常勤務
                    'user_id' => $this->user->id,
                    'company_id' => $companyId,
                ]));
            }
        };

        $firstWorkScheduleId = 2;
        $lastWorkScheduleId  = 4;

        //Act
        $result = count($this->attendanceService->getAttendanceRange($this->attendances, $firstWorkScheduleId, $lastWorkScheduleId));

        //Assert

        $expected = 3;
        $this->assertEquals($expected, $result);
    }
}
