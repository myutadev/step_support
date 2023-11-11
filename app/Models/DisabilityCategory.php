<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabilityCategory extends Model
{
    use HasFactory;

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }
}
