<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'mobile_number',
        'otp_verified', 'domain_name', 'price', 'payment_details'
    ];

    protected $casts = [
        'otp_verified' => 'boolean',
        'price' => 'decimal:2',
    ];
}
