<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainOrder extends Model
{
    protected $fillable = [
        'domain_name', 'price', 'first_name', 'last_name', 'email', 'mobile',
    ];
}
