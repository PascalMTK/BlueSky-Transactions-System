<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number', 'sender_name', 'sender_phone',
        'receiver_name', 'receiver_phone', 'amount', 'fee_percentage',
        'fee_amount', 'total_amount', 'currency', 'origin_country_id', 'destination_country_id',
        'agent_id', 'status', 'notes', 'payment_method', 'transaction_type', 'sent_at',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'fee_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'sent_at'        => 'datetime',
    ];

    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function getRouteCodeAttribute(): string
    {
        $origin = $this->originCountry?->code ?? '??';
        $dest   = $this->destinationCountry?->code ?? '??';
        return strtoupper($origin) . '-' . strtoupper($dest);
    }

    public static function generateTransactionNumber(): string
    {
        $prefix = 'BSK';
        $date   = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return "{$prefix}-{$date}-{$random}";
    }
}
