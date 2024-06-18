<?php

namespace App\Services;

use App\Models\Counselor;
use App\Models\DisabilityCategory;
use App\Models\Residence;
use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserWriteService
{
    protected $counselorRepository;
    protected $residenceRepository;
    protected $disabilityCategoryRepository;
    protected $userRepository;
    protected $AdminRepository;


    public function __construct(
        Counselor $counselorRepository,
        DisabilityCategory $disabilityCategoryRepository,
        Residence $residenceRepository,
        UserRepository $userRepository,
        AdminRepository $adminRepository
    ) {
        $this->counselorRepository = $counselorRepository;
        $this->residenceRepository = $residenceRepository;
        $this->disabilityCategoryRepository = $disabilityCategoryRepository;
        $this->userRepository = $userRepository;
        $this->AdminRepository = $adminRepository;
    }

    /**
     * users/createページの障害区分、住居、相談員の選択肢用のデータを返すメソッド
     *
     *@return array 障害区分･住居･相談員情報が入った連想配列。
     */
    public function getUserResistrationData()
    {
        return [
            'disabilityCategories' => $this->disabilityCategoryRepository->get(),
            'residences' => $this->residenceRepository->get(),
            'counselors' => $this->counselorRepository->get()
        ];
    }

    public function storeUser(Request $request): void
    {

        $companyId = $this->AdminRepository->getCurrentCompanyId();

        $user = $this->userRepository->createNewUser();
        $user->last_name = $request->last_name;
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        $userDetail = $this->userRepository->getUserDetailByUser($user);
        $userDetail->birthdate = $request->birthdate;
        $userDetail->beneficiary_number = $request->beneficiary_number;
        $userDetail->disability_category_id = $request->disability_category_id;
        //is_on_welfareの有無をチェック
        $userDetail->is_on_welfare = $request->is_on_welfare == 1 ? 1 : 0;

        $userDetail->residence_id = $request->residence_id;
        $userDetail->counselor_id = $request->counselor_id;
        $userDetail->admission_date = $request->admission_date;
        $userDetail->company_id = $companyId;
        $userDetail->update();
    }

    /**
     *ユーザー編集画面表示用のユーザーデータを取得する
     *ユーザーIDを元に、UserモデルからuserDetails, DisabilityCategory,Residnce,Counselor情報をリレーションで取得して返す。
     *@param int ブラウザからリクエストされたユーザーID
     *@return User ユーザー詳細、障害区分、相談員、住居名を付加したユーザーデータ
     */
    public function getUserWithDetailsByUserId($id)
    {
        $user = $this->userRepository->getUserWithDetailsByUserId($id);
        return $user;
    }
    /**
     * 編集したユーザーデータを保存する。
     *
     * リクエストとIDを受け取り、該当ユーザーの情報、UserDetailを編集する。
     *
     * @param Request $request ブラウザからリクエストされたユーザーの詳細データ
     * @param int $id ブラウザからリクエストされたユーザーID
     * @return void
     */
    public function updateUser(Request $request, $id): void
    {

        $companyId = $this->AdminRepository->getCurrentCompanyId();

        $user = $this->userRepository->getUserWithDetailsByUserId($id);
        $user->last_name = $request->last_name;
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->update();

        $user->userDetail->beneficiary_number = $request->beneficiary_number;
        $user->userDetail->disability_category_id = $request->disability_category_id;
        $user->userDetail->birthdate = $request->birthdate;
        //is_on_welfareの有無をチェック
        $user->userDetail->is_on_welfare = $request->is_on_welfare == 1 ? 1 : 0;
        $user->userDetail->residence_id = $request->residence_id;
        $user->userDetail->counselor_id = $request->counselor_id;
        $user->userDetail->admission_date = $request->admission_date;
        $user->userDetail->discharge_date = $request->discharge_date;
        $user->userDetail->company_id = $companyId;
        $user->userDetail->update();
    }
}
