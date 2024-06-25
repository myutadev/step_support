<?php

namespace App\Repositories;

use App\Models\Residence;
use Illuminate\Database\Eloquent\Collection;

class ResidenceRepository
{
    protected $residence;
    protected $adminRepository;

    public function __construct(Residence $residence,AdminRepository $adminRepository)
    {
        $this->residence = $residence;
        $this->adminRepository = $adminRepository;
    }

    public function get(): Collection
    {
        $companyId = $this->adminRepository->getCurrentCompanyId();
        return Residence::where('company_id', $companyId)->get();
    }

    public function create(): Residence
    {
        return new Residence();
    }

    public function getResidenceById($id)
    {
        return Residence::where('id', $id)->first();
    }

    public function deleteResidenceById($id): void
    {
        $counselor = $this->getResidenceById($id);
        $counselor->delete();
    }
}
