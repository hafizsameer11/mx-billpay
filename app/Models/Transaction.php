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
    public function billerItem()
    {
        return $this->hasOne(BillerItem::class);
    }
    public function transfer(){
        return $this->hasOne(Transfer::class);
    }


}
