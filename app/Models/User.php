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
        'email',
        'password','otp','otp_verified'
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
    public function Passreset(){
        return $this->hasOne(PasswordReset::class,'user_id','id');
    }
    public function account(){
        return $this->hasOne(Account::class,'user_id','id');
    }
    public function profile(){
        return $this->hasOne(Profile::class,'user_id','id');
    }
    public function notification(){
        return $this->hasMany(Notification::class,'user_id','id');
    }
    public function tranaction(){
        return $this->hasMany(Transaction::class,'user_id','id');
     }
     public function billPayment(){
        return $this->hasMany(BillPayment::class,'user_id','id');
    }
    public function pin(){
        return $this->hasMany(Pin::class,'user_id','id');
    }
}
