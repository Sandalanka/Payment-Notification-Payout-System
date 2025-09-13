<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_email',
        'original_amount',
        'original_currency',
        'amount_usd',
        'processed_at',
        'reference_no',
        'processed',
        // 'uploaded_by',
        // 'uploaded_file'
    ];

    protected $casts = [
        'processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(PaymentLog::class);
    }
}
