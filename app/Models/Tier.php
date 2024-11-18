<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'document_required',
        'description',
        'transaction_limit',
        'daily_limit',
        'balance_limit',
    ];
}
