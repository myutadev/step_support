<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function disabilityCategory()
    {
        return $this->belongsTo(DisabilityCategory::class);
    }
    public function residence()
    {
        return $this->belongsTo(Residence::class);
    }
    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }
}
