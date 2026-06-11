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

    public function getFlagEmojiAttribute(?string $value): string
    {
        if ($value) {
            return $value;
        }
        return self::codeToEmoji($this->attributes['code'] ?? '');
    }

    public static function codeToEmoji(string $code): string
    {
        $code = strtoupper(trim($code));
        if (strlen($code) !== 2) {
            return '';
        }
        $emoji = '';
        foreach (str_split($code) as $char) {
            $emoji .= mb_chr(0x1F1E6 + (ord($char) - ord('A')), 'UTF-8');
        }
        return $emoji;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->flag_emoji . ' ' . $this->name;
    }
}
