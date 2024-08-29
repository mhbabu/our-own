<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $table = 'otp_verifications';

    protected $fillable = [
        'user_id',
        'otp',
        'expire_at',
    ];

    public $timestamps = false;

    protected $dates = [
        'expire_at'
    ];
}
