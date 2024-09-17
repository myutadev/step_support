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

    public function getActiveUsers($allCompanyUsers, $dischargeDateCondition)
    {
        return $allCompanyUsers->filter(function ($user) use ($dischargeDateCondition) {
            return $user->userDetail->discharge_date >= $dischargeDateCondition || is_null(
                $user->userDetail->discharge_date
            );
        });
    }

    //このメソッドはいろいろなところで使い回すのでUserメソッドにしてよいのか疑問。Traitか別クラスを作るべき?
    // traitは検討したが、依存性が隠蔽されて良くないため、結局このクラスに残す。Adminサービスでも必要になれば作成
    public function getCompanyId(): int
    {
        return $this->AdminRepository->getCurrentCompanyId();
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
                'beneficiary_number_expiration' => $user->userDetail->beneficiary_number_expiration,
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

    /**
     *利用者アカウント管理画面に表示する用の利用者アカウントオブジェクトを並び替え
     *コントローラーから渡された$sortedField, $sortOrder情報を受け取り、指定のフィールドで並べ替えを行う
     *@return array ユーザー情報の入ったオブジェクト
     */

    public function sortUserAccountInfoObj($userInfoArray, $field, $order)
    {
        usort($userInfoArray, function ($a, $b) use ($field, $order) {
            if ($order == "asc") {
                return $a[$field] <=> $b[$field];
            } else {
                return $b[$field] <=> $a[$field];
            };
        });

        return $userInfoArray;
    }
}
