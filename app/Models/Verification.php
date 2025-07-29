<?php

namespace App\Models;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'domain_order_id',
        'reference_number',
        'description',
        'receipt_path',
    ];

    public function domainOrder()
    {
        return $this->belongsTo(DomainOrder::class);
    }
}



