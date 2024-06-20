<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\AdminDetail;
use Illuminate\Support\Facades\Auth;

class AdminRepository
{
    protected $admin;

    public function __construct(Admin $admin)
    {

        $this->admin = $admin;
    }

    public function getCurrentAdmin()
    {
        $currentAdminId = Auth::id();
        return Admin::with('adminDetail')->find($currentAdminId);
    }

    public function getCurrentCompanyId()
    {
        $admin = $this->getCurrentAdmin();
        return $admin->adminDetail->company_id;
    }

    public function getAdminId()
    {
        return Auth::id();
    }

    public function getAllAdminsByCompanyId()
    {
        $companyId = $this->getCurrentCompanyId();

        return Admin::whereHas('adminDetail', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['adminDetail.role'])->get();
    }

    public function create()
    {
        return new Admin();
    }

    public function getAdminDetail(): AdminDetail
    {
        return AdminDetail::where('admin_id', $this->admin->id)->first();
    }
}
