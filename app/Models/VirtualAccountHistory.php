<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualAccountHistory extends Model
{
    use HasFactory;
    protected $casts = [
        'expiryDate' => 'datetime',
    ];

}
