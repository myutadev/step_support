<?php

namespace App\Services;

use App\Models\Counselor;
use App\Models\DisabilityCategory;
use App\Models\Residence;
use App\Repositories\AdminRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class AdminWriteService
{
    protected $counselorRepository;
    protected $residenceRepository;
    protected $disabilityCategoryRepository;
    protected $userRepository;
    protected $AdminRepository;
    protected $roleRepository;



    public function __construct(
        Counselor $counselorRepository,
        DisabilityCategory $disabilityCategoryRepository,
        Residence $residenceRepository,
        UserRepository $userRepository,
        AdminRepository $adminRepository,
        RoleRepository $roleRepository
    ) {
        $this->counselorRepository = $counselorRepository;
        $this->residenceRepository = $residenceRepository;
        $this->disabilityCategoryRepository = $disabilityCategoryRepository;
        $this->userRepository = $userRepository;
        $this->AdminRepository = $adminRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * admin/adminsページの管理ユーザーの一覧を返すメソッド
     *
     *@return array 社員番号･名前･メールアドレス･役割･入社日･退社日
     */
    public function getAdminIndexData()
    {
        $adminInfoArray = [];

        $admins = $this->AdminRepository->getAllAdminsByCompanyId();
        foreach ($admins as $admin) {

            $curAdminInfo = [
                'emp_number' => $admin->adminDetail->emp_number,
                'name' => $admin->full_name,
                'email' => $admin->email,
                'role' => $admin->adminDetail->name,
                'hire_date' => $admin->adminDetail->hire_date,
                'termination_date' => $admin->adminDetail->termination_date,
                'admin_id' => $admin->id,
            ];
            array_push($adminInfoArray, $curAdminInfo);
        }

        return $adminInfoArray;
    }
    /**
     *admin/create用の役割データを取得する
     *
     *@return array 役割名が入ったrolesテーブルを全取得
     */
    public function getRoles()
    {
        return $this->roleRepository->get();
    }

    // public function storeUser(Request $request): void
    // {

    //     $companyId = $this->AdminRepository->getCurrentCompanyId();

    //     $user = $this->userRepository->createNewUser();
    //     $user->last_name = $request->last_name;
    //     $user->first_name = $request->first_name;
    //     $user->email = $request->email;
    //     $user->password = $request->password;
    //     $user->save();

    //     $userDetail = $this->userRepository->getUserDetailByUser($user);
    //     $userDetail->birthdate = $request->birthdate;
    //     $userDetail->beneficiary_number = $request->beneficiary_number;
    //     $userDetail->disability_category_id = $request->disability_category_id;
    //     //is_on_welfareの有無をチェック
    //     $userDetail->is_on_welfare = $request->is_on_welfare == 1 ? 1 : 0;

    //     $userDetail->residence_id = $request->residence_id;
    //     $userDetail->counselor_id = $request->counselor_id;
    //     $userDetail->admission_date = $request->admission_date;
    //     $userDetail->company_id = $companyId;
    //     $userDetail->update();
    // }

    // /**
    //  *ユーザー編集画面表示用のユーザーデータを取得する
    //  *ユーザーIDを元に、UserモデルからuserDetails, DisabilityCategory,Residnce,Counselor情報をリレーションで取得して返す。
    //  *@param int ブラウザからリクエストされたユーザーID
    //  *@return User ユーザー詳細、障害区分、相談員、住居名を付加したユーザーデータ
    //  */
    // public function getUserWithDetailsByUserId($id)
    // {
    //     $user = $this->userRepository->getUserWithDetailsByUserId($id);
    //     return $user;
    // }
    // /**
    //  * 編集したユーザーデータを保存する。
    //  *
    //  * リクエストとIDを受け取り、該当ユーザーの情報、UserDetailを編集する。
    //  *
    //  * @param Request $request ブラウザからリクエストされたユーザーの詳細データ
    //  * @param int $id ブラウザからリクエストされたユーザーID
    //  * @return void
    //  */
    // public function updateUser(Request $request, $id): void
    // {

    //     $companyId = $this->AdminRepository->getCurrentCompanyId();

    //     $user = $this->userRepository->getUserWithDetailsByUserId($id);
    //     $user->last_name = $request->last_name;
    //     $user->first_name = $request->first_name;
    //     $user->email = $request->email;
    //     $user->password = $request->password;
    //     $user->update();

    //     $user->userDetail->beneficiary_number = $request->beneficiary_number;
    //     $user->userDetail->disability_category_id = $request->disability_category_id;
    //     $user->userDetail->birthdate = $request->birthdate;
    //     //is_on_welfareの有無をチェック
    //     $user->userDetail->is_on_welfare = $request->is_on_welfare == 1 ? 1 : 0;
    //     $user->userDetail->residence_id = $request->residence_id;
    //     $user->userDetail->counselor_id = $request->counselor_id;
    //     $user->userDetail->admission_date = $request->admission_date;
    //     $user->userDetail->discharge_date = $request->discharge_date;
    //     $user->userDetail->company_id = $companyId;
    //     $user->userDetail->update();
    // }
}
