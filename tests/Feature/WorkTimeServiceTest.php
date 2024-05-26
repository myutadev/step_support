<?php

namespace Tests\Feature;

use App\Services\WorkTimeService;
use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkTimeServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testConvertDaysToHours(): void
    {
        // WorkTimeServiceのインスタンスを作成
        $workTimeService = new WorkTimeService();

        // テストデータの作成
        $interval = CarbonInterval::days(2)->hours(8)->minutes(15);
        $interval = $interval->invert(0);

        // メソッドの実行
        $newInterval = $workTimeService->convertDaysToHours($interval);

        // 結果の検証
        $this->assertEquals(56, $newInterval->hours);
        $this->assertEquals(15, $newInterval->minutes);
        $this->assertEquals(0, $newInterval->days);
        $this->assertEquals(0, $newInterval->invert);
    }
}
