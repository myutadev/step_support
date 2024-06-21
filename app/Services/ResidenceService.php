<?php

namespace App\Services;

use App\Http\Requests\ResidenceRequest;
use App\Repositories\AdminRepository;
use App\Repositories\ResidenceRepository;

class ResidenceService
{
    protected $adminRepository;
    protected $residenceRepository;

    public function __construct(
        AdminRepository $adminRepository,
        ResidenceRepository $residenceRepository

    ) {
        $this->residenceRepository = $residenceRepository;
        $this->adminRepository = $adminRepository;
    }

    public function getAllResidencesByCompanyId()
    {
        return $this->residenceRepository->get();
    }

    public function storeResidence(ResidenceRequest $request): void
    {
        $residence = $this->residenceRepository->create();
        $residence->name = $request->name;
        $residence->contact_phone = $request->contact_phone;
        $residence->contact_email = $request->contact_email;
        $residence->company_id = $this->adminRepository->getCurrentCompanyId();
        $residence->save();
    }

    public function getResidenceById($id)
    {
        return $this->residenceRepository->getResidenceById($id);
    }

    public function updateResidence(ResidenceRequest $request, $id): void
    {
        $residence = $this->getResidenceById($id);
        $residence->name = $request->name;
        $residence->contact_phone = $request->contact_phone;
        $residence->contact_email = $request->contact_email;
        $residence->update();
    }

    public function deleteResidence($id): void
    {
        $this->residenceRepository->deleteResidenceById($id);;
    }
}
