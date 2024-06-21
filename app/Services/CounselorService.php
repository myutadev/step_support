<?php

namespace App\Services;

use App\Http\Requests\CounselorRequest;
use App\Repositories\AdminRepository;
use App\Repositories\CounselorRepository;

class CounselorService
{
    protected $adminRepository;
    protected $counselorRepository;

    public function __construct(
        AdminRepository $adminRepository,
        CounselorRepository $counselorRepository

    ) {
        $this->counselorRepository = $counselorRepository;
        $this->adminRepository = $adminRepository;
    }

    public function getAllCounselorsByCompanyId()
    {
        return $this->counselorRepository->get();
    }

    public function storeCounselor(CounselorRequest $request): void
    {
        $counselor = $this->counselorRepository->create();
        $counselor->name = $request->name;
        $counselor->contact_phone = $request->contact_phone;
        $counselor->contact_email = $request->contact_email;
        $counselor->company_id = $this->adminRepository->getCurrentCompanyId();
        $counselor->save();
    }

    public function getCounselorById($id)
    {
        return $this->counselorRepository->getCounselorById($id);
    }

    public function updateCounselor(CounselorRequest $request, $id): void
    {
        $counselor = $this->getCounselorById($id);
        $counselor->name = $request->name;
        $counselor->contact_phone = $request->contact_phone;
        $counselor->contact_email = $request->contact_email;
        $counselor->update();
    }

    public function deleteCounselor($id): void
    {
        $this->counselorRepository->deleteCounselorById($id);;
    }
}
