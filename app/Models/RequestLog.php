<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'method', 'url', 'request_data', 'ip_address', 'user_agent', 'device_type'
    ];
}
