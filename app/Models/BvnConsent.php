<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BvnConsent extends Model
{
    use HasFactory;
    protected $fillable = [
        'bvn',
        'type',
        'user_id',
        'reference',
        'response',
    ];
}
