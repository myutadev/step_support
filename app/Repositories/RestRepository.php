<?php

namespace App\Repositories;

use App\Models\Rest;
use Illuminate\Database\Eloquent\Collection;

class RestRepository
{
    protected $rest;

    public function __construct(Rest $rest)
    {
        $this->rest = $rest;
    }

    public function create()
    {
        return new Rest();
    }
}
