<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'domain_name',
        'price',
        'category',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'unique_code',
        'payment_status',
    ];

    protected $dates = ['deleted_at'];

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    // Relationship to SMS logs related to this order
    public function smsLogs()
    {
        return $this->hasMany(InvoiceSmsLog::class, 'invoice_id');
    }
}
