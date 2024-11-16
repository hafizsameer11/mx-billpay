<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CooperateAccountRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name',
        'company_address',
        'rc_number',
        'cac_certificate',
        'business_address_verification',
        'director_id_verification',
        'director_nin_number',
        'director_bvn_number',
        'director_dob',
    ];

}
