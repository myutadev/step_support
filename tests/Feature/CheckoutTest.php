<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\Attendance;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class CheckoutTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_checkout_before_12(): void
    {
        $user = User::factory()->create();
        $workSchedule = WorkSchedule::factory()->create(['date' => Carbon::today()]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_schedule_id' => $workSchedule->id,
            'check_in_time' => '09:00:00' // 任意の時間を設定,
        ]);

        // テスト用の現在時刻を設定
        $testNow = Carbon::create(2024, 1, 16, 11, 55);
        Carbon::setTestNow($testNow);

        // リクエストデータの準備
        $requestData = [
            'work_description' => 'インスタやった',
            'work_comment' => 'たのしくなかった'
        ];

        $this->actingAs($user);

        $response = $this->post(route('attendances.checkout'), $requestData);

        //リダイレクトがうまく言っているか?

        $response->assertRedirect(route('attendances.index'));

        //データベースに期待する値が含まれているか?
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'check_out_time' => $testNow->toTimeString(),
            'work_description' => 'インスタやった',
            'work_comment' => 'たのしくなかった'

        ]);

        // テスト用の現在時刻をリセット
        Carbon::setTestNow();
    }
}
