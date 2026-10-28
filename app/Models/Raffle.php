<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Raffle extends Model
{
    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'drawn_at' => 'datetime',
        ];
    }

    public function numbers(): HasMany
    {
        return $this->hasMany(RaffleNumber::class)->orderBy('number');
    }

    public function winnerNumber(): BelongsTo
    {
        return $this->belongsTo(RaffleNumber::class, 'winner_number_id');
    }
}
