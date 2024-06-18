<?php

namespace App\Repositories;

use App\Models\Residence;
use Illuminate\Database\Eloquent\Collection;

class ResidenceRepository
{
    protected $residence;

    public function __construct(Residence $residence)
    {
        $this->residence = $residence;
    }

    public function get(): Collection
    {
        return Residence::get();
    }
}
