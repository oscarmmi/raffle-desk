<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaffleNumber extends Model
{
    public const STATUS_AVAILABLE = 'available';

    public const STATUS_RESERVED = 'reserved';

    public const STATUS_SOLD = 'sold';

    protected $fillable = [
        'number',
        'status',
        'buyer_name',
        'buyer_phone',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
        ];
    }

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }

    public function displayNumber(): string
    {
        return str_pad((string) $this->number, 2, '0', STR_PAD_LEFT);
    }
}
