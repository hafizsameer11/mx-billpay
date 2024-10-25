<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillerItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id', 'paymentitemname', 'paymentCode', 'productId',
        'paymentitemid', 'currencySymbol', 'isAmountFixed', 'itemFee',
        'itemCurrencySymbol', 'pictureId', 'billerType', 'payDirectitemCode',
        'currencyCode', 'division', 'fixed_commission', 'percentage_commission','billerId'
    ];

    public function category()
    {
        return $this->belongsTo(BillerCategory::class);
    }
}
