<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transfer()
    {
        return $this->hasOne(Transfer::class);
    }
    public function billPayment()
    {
        return $this->hasOne(BillPayment::class);
    }
    public function account(){
        return $this->belongsTo(Account::class,'user_id','id');
    }
}
