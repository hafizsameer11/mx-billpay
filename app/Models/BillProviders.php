<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillProviders extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'biller_category_id',
        'division',
        'product',
        'name',
        'billerId'
    ];

    public function category()
    {
        return $this->belongsTo(BillerCategory::class, 'biller_category_id', 'id');
    }
}
