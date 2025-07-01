<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainPrice extends Model
{
    protected $table = 'domain_prices';
    protected $fillable = ['category', 'old_price', 'new_price'];
    public $timestamps = true;
}
