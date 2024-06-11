<?php

namespace Tests\Feature;

use App\Domains\Attendance\DailyAdminComment;
use App\Models\AdminComment;
use Database\Seeders\AdminCommentSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\AttendanceSeeder;
use Database\Seeders\AttendanceTypeSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ScheduleTypeSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\WorkScheduleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class DailyAdminCommentTest extends TestCase
{
    use RefreshDatabase;


    public function testShowAllComments(): void
    {
        $this->seed(CompanySeeder::class);
        $this->seed(AttendanceTypeSeeder::class);
        $this->seed(ScheduleTypeSeeder::class);
        $this->seed(WorkScheduleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->seed(AttendanceSeeder::class);
        $this->seed(AdminCommentSeeder::class);

        $dailyAdminComment = new DailyAdminComment();
        $adminComment1 = AdminComment::with('admin')->find(1);
        $adminComment2 = AdminComment::with('admin')->find(2);

        $dailyAdminComment->push($adminComment1);
        $dailyAdminComment->push($adminComment2);

        $allCommentsStr = $dailyAdminComment->showAllComments();
        $expected = $adminComment1->admin_description . " :" . $adminComment1->admin_comment . " (" . $adminComment1->admin->full_name . ")"
            . "<br>" . $adminComment2->admin_description . " :" . $adminComment2->admin_comment . " (" . $adminComment2->admin->full_name . ")";
        assertEquals($allCommentsStr, $expected);
    }
}
