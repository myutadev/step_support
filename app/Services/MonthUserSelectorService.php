<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class MonthUserSelectorService
{
    protected $userRepository;
    protected $adminRepository;
    protected $workScheduleRepository;

    public function __construct(
        UserRepository $userRepository,
        AdminRepository $adminRepository,
        WorkScheduleRepository $workScheduleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->adminRepository = $adminRepository;
        $this->workScheduleRepository = $workScheduleRepository;
    }

    private function getCurrentCompanyId(): int
    {
        return $this->adminRepository->getCurrentCompanyId();
    }

    private function getUsersByCompanyId(): Collection
    {
        return  $this->userRepository->getUsersByCompanyId($this->getCurrentCompanyId());
    }

    public function getSelectedYearMonth($yearmonth): array
    {
        if ($yearmonth == null) {
            $today = Carbon::today();
            $year = $today->year;
            $month = sprintf("%02d", $today->month);
        } else {
            $yearMonthArr = explode("-", $yearmonth);
            $year = $yearMonthArr[0];
            $month = sprintf("%02d", $yearMonthArr[1]);
        }

        $selectedYearMonth = [
            'year' => $year,
            'month' => $month
        ];
        return $selectedYearMonth;
    }

    private function getSelectedUserId($user_id = null): int
    {
        $companyId = $this->getCurrentCompanyId();
        if ($user_id == null) {
            return $this->userRepository->getFirstUserByCompanyId($companyId)->id;
        }
        return $user_id;
    }

    public function createMonthUserSelectorDataObj($yearmonth, $user_id): array
    {
        $users = $this->getUsersByCompanyId();
        $selectedYearMonth = $this->getSelectedYearMonth($yearmonth);
        $year = $selectedYearMonth["year"];
        $month = $selectedYearMonth["month"];
        $user_id = $this->getSelectedUserId($user_id);
        return [
            'users' => $users,
            'year' => $year,
            'month' => $month,
            'user_id' => $user_id,
        ];
    }

    public function generateSelectedYearMonthByWorkSchedId($id)
    {
        $targetWorkSchedule = $this->workScheduleRepository->getWorkScheduleById($id);
        $year = $targetWorkSchedule->year;
        $month = sprintf("%02d", $targetWorkSchedule->month);
        return  $year . "-" . $month;
    }
    public function generateSelectedYearMonthBySpecialScheduleId($id)
    {

        $targetWorkSchedule = $this->workScheduleRepository->getWorkScheduleBySpecialScheduleId($id);
        $year = $targetWorkSchedule->year;
        $month = sprintf("%02d", $targetWorkSchedule->month);
        return  $year . "-" . $month;
    }
}
