<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    //userインスタンスが作られたら自動で紐づいたuser_detailsテーブルのインスタンスを作成
    protected static function boot()
    {

        parent::boot();

        static::created(function ($user) {

            $user->userDetail()->create([
                // 必要に応じてデフォルト値を設定する

            ]);
        });
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function getFullNameAttribute()
    {
        return $this->last_name . $this->first_name;
    }
}
