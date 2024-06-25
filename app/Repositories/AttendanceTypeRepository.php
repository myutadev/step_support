<?php

namespace App\Repositories;

use App\Models\AttendanceType;
use Illuminate\Database\Eloquent\Collection;

class AttendanceTypeRepository
{
    private $attendanceType;

    public function __construct(AttendanceType $attendanceType)
    {
        $this->attendanceType = $attendanceType;
    }

    public function getLeaveTypes(): Collection
    {
        return $this->attendanceType->where('name', 'LIKE', '%欠勤%')->orWhere('name', 'LIKE', '%有給%')->get();
    }

    public function getLeaveTypesIds(): array
    {
        return $this->getLeaveTypes()->pluck('id')->toArray();
    }
    public function getAllAttendaneType()
    {
        return AttendanceType::all();
    }
}
