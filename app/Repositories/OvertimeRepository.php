<?php

namespace App\Repositories;

use App\Models\Overtime;
use Illuminate\Database\Eloquent\Collection;

class OvertimeRepository
{
    protected $overtime;

    public function __construct(Overtime $overtime)
    {
        $this->overtime = $overtime;
    }

    public function create()
    {
        return new Overtime();
    }
}
