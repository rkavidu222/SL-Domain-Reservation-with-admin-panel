<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceSmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'phone',
        'message',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(DomainOrder::class, 'invoice_id');
    }

}
