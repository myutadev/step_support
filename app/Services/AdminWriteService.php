<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\DisabilityCategory;
use App\Models\Residence;
use App\Repositories\AdminRepository;
use App\Repositories\CounselorRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminWriteService
{
    protected $counselorRepository;
    protected $residenceRepository;
    protected $disabilityCategoryRepository;
    protected $userRepository;
    protected $AdminRepository;
    protected $roleRepository;



    public function __construct(
        CounselorRepository $counselorRepository,
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
    public function getAdminIndexData():array
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
    public function getRoles(): Collection
    {
        return $this->roleRepository->get();
    }

    public function storeAdmin(Request $request): void
    {
        $companyId = $this->AdminRepository->getCurrentCompanyId();
        $admin = $this->AdminRepository->create();

        $admin->last_name = $request->last_name;
        $admin->first_name = $request->first_name;
        $admin->email = $request->email;
        $admin->password = $request->password;
        $admin->save();

        $adminDetail = $admin->adminDetail;
        $adminDetail->hire_date = $request->hire_date;
        $adminDetail->emp_number = $request->emp_number;
        $adminDetail->role_id = $request->role_id;
        $adminDetail->company_id = $companyId;
        $adminDetail->update();
    }

    public function getEditAdminData($id): Admin
    {
        return $this->AdminRepository->getAdminById($id);
    }

    public function updateAdmin(Request $request, $id): void
    {

        $admin = $this->AdminRepository->getAdminById($id);
        $admin->last_name = $request->last_name;
        $admin->first_name = $request->first_name;
        $admin->email = $request->email;
        $admin->password = $request->password;
        $admin->update();

        $admin->adminDetail->hire_date = $request->hire_date;
        $admin->adminDetail->termination_date = $request->termination_date;
        $admin->adminDetail->emp_number = $request->emp_number;
        $admin->adminDetail->role_id = $request->role_id;
        $admin->adminDetail->update();
    }
}
