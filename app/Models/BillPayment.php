<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'biller_item_id',
        'user_id',
        'status',
        'refference',
        'customerId',
        'phoneNumber'
    ];
    public function billerItem(){
        return $this->belongsTo(BillerItem::class,'biller_item_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}