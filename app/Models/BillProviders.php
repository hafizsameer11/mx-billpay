<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillProviders extends Model
{
    use HasFactory;
    protected $fillable=['title','slug','biller_category_id'];


    public function category(){
        return $this->hasMany(BillerCategory::class,'id','biller_category_id ');
    }
}
