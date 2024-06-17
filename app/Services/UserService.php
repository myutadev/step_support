<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    protected $UserRepository;
    protected $AdminRepository;

    public function __construct(UserRepository $userRepository, AdminRepository $adminRepository)
    {
        $this->UserRepository = $userRepository;
        $this->AdminRepository = $adminRepository;
    }

    public function getCompanyUsers(): Collection
    {
        $companyId = $this->AdminRepository->getCurrentCompanyId();
        return  $this->UserRepository->getUsersByCompanyId($companyId);
    }

    function getActiveUsers($allCompanyUsers, $dischargeDateCondition)
    {
        return $allCompanyUsers->filter(function ($user) use ($dischargeDateCondition) {
            return $user->userDetail->discharge_date >= $dischargeDateCondition || is_null(
                $user->userDetail->discharge_date
            );
        });
    }

    /**
     *利用者アカウント管理画面に表示する用の利用者アカウントオブジェクトを作成
     *現在ログイン中の管理者IDからCompanyIDを取得し、そのIDに紐づくUsersアカウントを全て取得
     *@return array ユーザー情報の入ったオブジェクト
     */

    public function createUserAccountInfoObj(): array
    {
        $users = $this->getCompanyUsers();

        $userInfoArray = [];
        foreach ($users as $user) {

            $curUserInfo = [
                'beneficiary_number' => $user->userDetail->beneficiary_number,
                'name' => $user->full_name,
                'email' => $user->email,
                'is_on_welfare' => $user->userDetail->is_on_welfare == 1 ? "有" : "無",
                'admission_date' => $user->userDetail->admission_date,
                'discharge_date' => $user->userDetail->discharge_date,
                'birthdate' => $user->userDetail->birthdate,
                'disability_category_id' => $user->userDetail->disabilityCategory->name,
                'residence_id' => $user->userDetail->residence->name,
                'counselor_id' => $user->userDetail->counselor->name,
                'user_id' => $user->id,

            ];
            array_push($userInfoArray, $curUserInfo);
        }

        return $userInfoArray;
    }
}
