<?php

namespace App\Services;

use App\Repositories\AdminCommentRepository;
use App\Repositories\AdminRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\AttendanceTypeRepository;
use App\Repositories\OvertimeRepository;
use App\Repositories\RestRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AttendanceService
{
    protected $attendanceTypeRepository;
    protected $attendanceRepository;
    protected $restRepository;
    protected $overtimeRepository;
    protected $adminRepository;
    protected $adminCommentRepository;

    public function __construct(
        AttendanceTypeRepository $attendanceTypeRepository,
        AttendanceRepository $attendanceRepository,
        RestRepository $restRepository,
        OvertimeRepository $overtimeRepository,
        AdminRepository $adminRepository,
        AdminCommentRepository $adminCommentRepository,
    ) {
        $this->attendanceTypeRepository = $attendanceTypeRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->restRepository = $restRepository;
        $this->overtimeRepository = $overtimeRepository;
        $this->adminRepository = $adminRepository;
        $this->adminCommentRepository = $adminCommentRepository;
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

    public function generateEditAttendaceData()
    {
        return $this->attendanceTypeRepository->getAllAttendaneType();
    }

    public function getAttendanceById($id)
    {
        return  $this->attendanceRepository->getAttendanceById($id);
    }

    public function updateAttendance(Request $request, $id)
    {
        $attendance = $this->getAttendanceById($id);
        $attendance->body_temp = $request->body_temp;
        $attendance->check_in_time = $request->check_in_time;
        $attendance->check_out_time = $request->check_out_time;
        $attendance->is_overtime = $request->is_overtime;
        $attendance->attendance_type_id = $request->attendance_type;
        $counter = 1;

        foreach ($attendance->rests as $rest) {
            $restStartKey = "rest_start_" . $counter;
            $restEndKey = "rest_end_" . $counter;
            $rest->start_time = $request->$restStartKey;
            $rest->end_time = $request->$restEndKey;
            $rest->update();
            $counter++;
        }

        $counter = 1;
        foreach ($attendance->overtimes as $overtime) {
            $overtimeStartKey = "overtime_start_" . $counter;
            $overtimeEndKey = "overtime_end_" . $counter;
            $overtime->start_time = $request->$overtimeStartKey;
            $overtime->end_time = $request->$overtimeEndKey;
            $overtime->update();
            $counter++;
        }

        if ($request->rest_start_add) {
            $newRest = $this->restRepository->create();
            $newRest->attendance_id = $id;
            $newRest->start_time = $request->rest_start_add;
            $newRest->end_time = $request->rest_end_add;
            // dd($newRest);
            $newRest->save();
        }

        if ($request->overtime_start_add) {
            $newOvertime = $this->overtimeRepository->create();
            $newOvertime->attendance_id = $id;
            $newOvertime->start_time = $request->overtime_start_add;
            $newOvertime->end_time = $request->overtime_end_add;
            // dd($newRest);
            $newOvertime->save();
        }

        $attendance->update();
    }

    public function storeLeaveRecord(Request $request, $user_id, $sched_id)
    {

        $companyId = $this->adminRepository->getCurrentCompanyId();

        $attendance = $this->attendanceRepository->create();
        $attendance->attendance_type_id = $request->leave_type_id;
        $attendance->user_id = $user_id;
        $attendance->work_schedule_id = $sched_id;
        $attendance->company_id = $companyId;

        $attendance->save();


        $admin_comment = $this->adminCommentRepository->getAdminCommentByAttendanceId($attendance->id);
        $admin_comment->admin_description = $request->admin_description;
        $admin_comment->admin_comment = $request->admin_comment;
        $admin_comment->admin_id = $this->adminRepository->getAdminId();
        $admin_comment->update();

        $yearmonth = $request->yearmonth;
    }
}
