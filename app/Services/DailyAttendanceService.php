<?php

namespace App\Services;

use App\Models\AdminComment;
use App\Models\Attendance;
use App\Models\WorkSchedule;
use App\Repositories\AdminCommentRepository;
use App\Repositories\AdminRepository;
use App\Repositories\WorkScheduleRepository;
use App\Traits\AttendanceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class DailyAttendanceService
{
    use AttendanceTrait;

    protected $adminRepository;
    protected $workScheduleRepository;
    protected $adminCommentRepository;
    protected $date;

    public function __construct(
        AdminRepository $adminRepository,
        WorkScheduleRepository $workScheduleRepository,
        AdminCommentRepository $adminCommentRepository,
        String $date = null
    ) {
        $this->adminRepository = $adminRepository;
        $this->workScheduleRepository = $workScheduleRepository;
        $this->adminCommentRepository = $adminCommentRepository;
        $this->date = $date;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function generateSelectedDate(): string
    {
        if ($this->date == null) {
            $today = Carbon::today();
            return  $today->year . "-" . sprintf("%02d", $today->month) . "-" . sprintf("%02d", $today->day);
        } else {
            return $this->date;
        };
    }

    /**
     * 指定したCompanyId, 日付のWorkshceduleのコレクションを返す
     * 
     *@return Collection Workscheduleのコレクション、Attendance情報等が紐づいている
     */
    public function generateSelectedWorkScheduleData()
    {
        $companyId = $this->adminRepository->getCurrentCompanyId();
        $selectedDate = $this->generateSelectedDate();

        return  $this->workScheduleRepository->generateDailyAttenanceData($companyId, $selectedDate);
    }

    /**
     *生成したworkScheduleコレクションから、出席データを取得する。出席データが何もない場合はnullを返す
     *
     *@return Collection 様々な情報をイーガーロードしたAttendance
     */
    private function generateSelectedAttendances()
    {
        $selectedWorkSched = $this->generateSelectedWorkScheduleData();
        return $selectedWorkSched == null ? null : $selectedWorkSched->attendances;
    }

    /**
     *AdminComments内で自身がコメントしたレコードが最後になるようにソートする関数
     *
     *@return void
     */
    private function sortAdminCommentsByAdminId($selectedAttendances): void
    {
        $adminId = $this->adminRepository->getAdminId();
        foreach ($selectedAttendances as $attendance) {
            $sortedAdminComments = $attendance->adminComments->sortBy(function ($comment) use ($adminId) {
                return $comment->admin_id == $adminId ? 1 : 0;
            });

            $attendance->adminComments = $sortedAdminComments;
            // dd($attendance->adminComments);
        }
    }

    /**
     *日別出勤状況に表示するデータを作成する
     *
     *レコードがない場合は空の配列を返す。、
     *@return array 表示用の連想配列を含んだ配列
     */
    public function generateDailyAttenanceData()
    {
        $dailyAttendanceData = [];
        $selectedAttendances = $this->generateSelectedAttendances();

        if ($selectedAttendances == null) return $dailyAttendanceData;

        $this->sortAdminCommentsByAdminId($selectedAttendances);

        foreach ($selectedAttendances as $curAttendance) {
            $curAttendanceRecord = [
                'attendance_id' => $curAttendance->id,
                'beneficialy_number' => $curAttendance->user->userDetail->beneficiary_number,
                'name' => $curAttendance->user->full_name,
                'body_temp' => $curAttendance->body_temp,
                'check_in_time' => $curAttendance->check_in_time,
                'check_out_time' => $curAttendance->check_out_time,
                'rest' => $this->generateStartEndString($curAttendance->rests),
                'over_time' => $this->generateStartEndString($curAttendance->overtimes),
                'work_description' => $curAttendance->work_description,
                'work_comment' => $curAttendance->work_comment,
                'admin_comments' => $curAttendance->adminComments,
            ];
            array_push($dailyAttendanceData, $curAttendanceRecord);
        }
        return $dailyAttendanceData;
    }

    /**
     *日別出勤状況画面で管理者コメントを更新するメソッド
     *
     *
     *@return view
     */
    public function updateAdminComment(Request $request, AdminComment $admincomment)
    {
        $admin_id = $this->adminRepository->getAdminId();
        $adminComment = $this->adminCommentRepository->getAdminCommentById($admincomment->id);
        $workSchedule = $this->workScheduleRepository->getWorkScheduleByAdminComment($adminComment);
        $date = $workSchedule->date;


        if ($request->user()->cannot('update', $adminComment)) {
            return redirect()->route('admin.daily', compact('date'))->withErrors('自分のコメント以外は更新できません');
        }

        $adminComment->admin_description = $request->admin_description;
        $adminComment->admin_comment = $request->admin_comment;
        $adminComment->admin_id = $admin_id;
        $adminComment->update();
        return redirect()->route('admin.daily', compact('date'));
    }

    //これは本当はView側でdateを送れば済むけど、一旦こっちのほうが早いためこれで処理する。
    public function getDateByAdminComment($admincomment)
    {
        $adminComment = $this->adminCommentRepository->getAdminCommentById($admincomment->id);
        $workSchedule = $this->workScheduleRepository->getWorkScheduleByAdminComment($adminComment);
        return $workSchedule->date;
    }

    public function storeAdminComment(Request $request, Attendance $attendance): void
    {
        $admin_id = $this->adminRepository->getAdminId();

        $adminComment = $this->adminCommentRepository->createNewAdminComment();
        $adminComment->attendance_id = $attendance->id;
        $adminComment->admin_id = $admin_id;
        $adminComment->admin_description = $request->admin_description;
        $adminComment->admin_comment = $request->admin_comment;
        $adminComment->save();
    }

    //これは本当はView側でdateを送れば済むけど、一旦こっちのほうが早いためこれで処理する。
    public function getDateByAttendance($attendance)
    {
        $workSchedule = $this->workScheduleRepository->getWorkScheduleByAttendance($attendance);
        return $workSchedule->date;
    }
}
