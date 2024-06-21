<?php

namespace App\Repositories;

use App\Models\Counselor;
use Illuminate\Database\Eloquent\Collection;

class CounselorRepository
{
    protected $counselor;
    protected $adminRepository;

    public function __construct(Counselor $counselor, AdminRepository $adminRepository)
    {
        $this->counselor = $counselor;
        $this->adminRepository = $adminRepository;
    }

    public function get(): Collection
    {
        $companyId = $this->adminRepository->getCurrentCompanyId();
        return Counselor::where('company_id', $companyId)->get();
    }

    public function create(): Counselor
    {
        return new Counselor();
    }

    public function getCounselorById($id)
    {
        return Counselor::where('id', $id)->first();
    }

    public function deleteCounselorById($id): void
    {
        $counselor = $this->getCounselorById($id);
        $counselor->delete();
    }
}
