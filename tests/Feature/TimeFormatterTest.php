<?php

namespace Tests\Feature;

use App\Utils\TimeFormatter;
use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class TimeFormatterTest extends TestCase
{
    /**
     * CarbonInterval形式を画面表示用にテキストに変換
     */
    public function testCarbonIntervalToStringHours(): void
    {
        $testCarbonInterval = CarbonInterval::hour(2)->minutes(25);
        $result = TimeFormatter::carbonIntervalToStringHours($testCarbonInterval);
        assertEquals("02:25:00", $result);
    }

    public function testConvertDaysToHours():void
    {
        $interval = CarbonInterval::days(2)->hours(8)->minutes(15);
        $interval = $interval->invert(0);

        // メソッドの実行
        $newInterval = TimeFormatter::convertDaysToHours($interval);

        // 結果の検証
        $this->assertEquals(56, $newInterval->hours);
        $this->assertEquals(15, $newInterval->minutes);
        $this->assertEquals(0, $newInterval->days);
        $this->assertEquals(0, $newInterval->invert);
    }

}
