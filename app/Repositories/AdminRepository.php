<?php

namespace App\Repositories;

use App\Models\Admin;
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

    
}
