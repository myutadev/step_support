<?php

namespace App\Repositories;

use App\Models\DisabilityCategory;
use Illuminate\Database\Eloquent\Collection;

class DisabilityCategoryRepository
{
    protected $disabilityCategory;

    public function __construct(DisabilityCategory $disabilityCategory)
    {
        $this->disabilityCategory = $disabilityCategory;
    }

    public function get(): Collection
    {
        return DisabilityCategory::get();
    }
}
