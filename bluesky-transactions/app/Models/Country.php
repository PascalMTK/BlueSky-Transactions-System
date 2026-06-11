<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name', 'code', 'currency_code', 'currency_name',
        'flag_emoji', 'phone_code', 'default_fee_percentage', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_fee_percentage' => 'decimal:2',
    ];

    public function agents(): HasMany
    {
        return $this->hasMany(User::class, 'country_id');
    }

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'origin_country_id');
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_country_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->flag_emoji . ' ' . $this->name;
    }
}
