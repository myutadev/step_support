<?php

namespace App\Domains\Attendance;

use App\Domains\Attendance\TimeSlot;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class DailyTimeSlot implements DailyTimeSlotInterface
{
    protected int $attendanceId;
    protected array $timeSlots;

    //nullオブジェクトはここで創るのが良い?
    public function __construct(int $attendanceId)
    {
        $this->attendanceId = $attendanceId;
        $this->timeSlots = [];
    }


    public function push(TimeSlot $timeSlot): void
    {
        $this->timeSlots[] = $timeSlot;
    }

    public function getAttendanceId(): int
    {
        return $this->attendanceId;
    }

    public function getTimeSlots(): array
    {
        return $this->timeSlots;
    }

    /**
     * 1日のすべてのレコードをStringで表示
     *
     * @return string
     */
    public function showAllTimeSlotsStr(): string
    {
        $allTimeSlotsStrArray = [];
        if (count($this->timeSlots) === 0) return "";
        foreach ($this->timeSlots as $timeSlot) {
            $allTimeSlotsStrArray[] = $timeSlot->getStartTime()->format('H:i') . '-' . $timeSlot->getEndTime()->format('H:i');
        }
        $allTimeSlotsStr = implode("<br>", $allTimeSlotsStrArray);

        return $allTimeSlotsStr;
    }

    public function sumTotalDuration(): CarbonInterval
    {
        //休憩時間は繰り上げ、繰り下げしない。最後に勤務時間を出すときに15分単位で繰り下げる。
        //このロジックは花村さんに要確認!
        $totalDuration = CarbonInterval::minutes(0);
        //休憩or残業が存在しない場合→timeSlotsが空の配列
        if (count($this->timeSlots) == 0) return $totalDuration;


        foreach ($this->timeSlots as $timeSlot) {
            $start = $timeSlot->getStartTime();
            $end = $timeSlot->getEndTime();
            $duration = $start->diff($end);
            $totalDuration->add($duration);
        }

        return $totalDuration;
    }
}
