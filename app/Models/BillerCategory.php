<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillerCategory extends Model
{
    use HasFactory;
    protected $fillable = ['category', 'fixed_commission', 'percentage_commission'];
    public function items()
    {
        return $this->hasMany(BillerItem::class);
    }
    public function bill_payment()
    {
        return $this->hasMany(BillPayment::class);
    }
}
