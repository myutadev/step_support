<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class MonthUserSelectorService
{
    protected $userRepository;
    protected $adminRepository;

    public function __construct(UserRepository $userRepository, AdminRepository $adminRepository)
    {
        $this->userRepository = $userRepository;
        $this->adminRepository = $adminRepository;
    }

    private function getCurrentCompanyId(): int
    {
        return $this->adminRepository->getCurrentCompanyId();
    }

    public function getUsersByCompanyId(): Collection
    {
        return  $this->userRepository->getUsersByCompanyId($this->getCurrentCompanyId());
    }

    public function getSelectedYearMonth($yearmonth)
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

    public function getSelectedUserId($user_id = null)
    {
        $companyId = $this->getCurrentCompanyId();
        if ($user_id == null) {
            return $this->userRepository->getFirstUserByCompanyId($companyId);
        }
        return $user_id;
    }
}
