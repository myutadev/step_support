<?php

namespace App\Repositories;

use App\Models\Counselor;
use Illuminate\Database\Eloquent\Collection;

class ResidenceRepository
{
    protected $counselor;

    public function __construct(Counselor $counselor)
    {
        $this->counselor = $counselor;
    }

    public function get(): Collection
    {
        return Counselor::get();
    }
}
