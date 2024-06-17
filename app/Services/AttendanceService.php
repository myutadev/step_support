<?php

namespace App\Services;

use App\Repositories\AttendanceTypeRepository;
use Illuminate\Database\Eloquent\Collection;

class AttendanceService
{
    protected $attendanceTypeRepository;

    public function __construct(AttendanceTypeRepository $attendanceTypeRepository)
    {
        $this->attendanceTypeRepository = $attendanceTypeRepository;
    }

    public function getLeaveTypes(): Collection
    {
        return $this->attendanceTypeRepository->getLeaveTypes();
    }
    public function getLeaveTypeIds(): array
    {
        return $this->attendanceTypeRepository->getLeaveTypesIds();
    }



    public static function getAttendanceRange(Collection $attendances, int $firstWorkScheduleId, int $lastWorkScheduleId): Collection
    {
        return  $attendances->filter(function ($attendance) use ($firstWorkScheduleId, $lastWorkScheduleId) {
            return
                $attendance->work_schedule_id >= $firstWorkScheduleId
                && $attendance->work_schedule_id <= $lastWorkScheduleId;
        });
    }

    public function getPresentAttendance(Collection $attendances): Collection
    {
        return $attendances->filter(function ($attendance) {
            return ($attendance->attendance_type_id == 1 || $attendance->attendance_type_id == 2);
        });
    }
}
