<?php

namespace Tests\Feature;

use App\Domains\Attendance\DailyTimeSlot;
use App\Domains\Attendance\TimeSlot;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class DailyTimeSlotTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function testGetTimeSlots(): void
    {
        $dailyTimeSlots =  new DailyTimeSlot(1);

        $timeSlots1 = new TimeSlot(Carbon::parse('12:00:00'), Carbon::parse('13:00:00'));
        $timeSlots2 = new TimeSlot(Carbon::parse('13:30:00'), Carbon::parse('13:48:00'));

        $dailyTimeSlots->push($timeSlots1);
        $dailyTimeSlots->push($timeSlots2);

        $expected = new TimeSlot(Carbon::parse('12:00:00'), Carbon::parse('13:00:00'));
        assertEquals($expected, $dailyTimeSlots->getTimeSlots()[0]);
    }


    public function testShowAllTimeSlotStr_returnString(): void
    {
        $dailyTimeSlots =  new DailyTimeSlot(1);

        $timeSlots1 = new TimeSlot(Carbon::parse('12:00:00'), Carbon::parse('13:00:00'));
        $timeSlots2 = new TimeSlot(Carbon::parse('13:30:00'), Carbon::parse('13:48:00'));

        $dailyTimeSlots->push($timeSlots1);

        $result1 = $dailyTimeSlots->showAllTimeSlotsStr();
        assertEquals('12:00-13:00', $result1);

        $dailyTimeSlots->push($timeSlots2);

        $result2 = $dailyTimeSlots->showAllTimeSlotsStr();

        assertEquals('12:00-13:00<br>13:30-13:48', $result2);
    }

    public function testSumTotalDuration_returnCarbonInterval(): void
    {

        $dailyTimeSlots =  new DailyTimeSlot(1);

        $timeSlots1 = new TimeSlot(Carbon::parse('12:00:00'), Carbon::parse('13:00:00'));
        $timeSlots2 = new TimeSlot(Carbon::parse('13:30:00'), Carbon::parse('13:48:00'));
        $dailyTimeSlots->push($timeSlots1);
        $dailyTimeSlots->push($timeSlots2);
        $result = $dailyTimeSlots->sumTotalDuration();

        $timeString = '01:18:00';
        list($hours, $minutes, $seconds) = explode(':', $timeString);
        $expectedResult = CarbonInterval::hours($hours)->minutes($minutes)->seconds($seconds);



        assertEquals($expectedResult, $result);
    }
}
