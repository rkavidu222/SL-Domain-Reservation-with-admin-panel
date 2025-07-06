<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'domain_name', 'price', 'category', 'first_name', 'last_name', 'email', 'mobile',
    ];

    protected $dates = ['deleted_at'];
}
