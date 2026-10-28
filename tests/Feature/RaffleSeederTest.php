<?php

use App\Models\Raffle;
use App\Models\RaffleNumber;
use Database\Seeders\RaffleSeeder;

it('creates one hundred raffle numbers with forty seeded sales', function () {
    $this->seed(RaffleSeeder::class);

    expect(Raffle::query()->count())->toBe(1)
        ->and(RaffleNumber::query()->count())->toBe(100)
        ->and(RaffleNumber::query()->where('status', RaffleNumber::STATUS_SOLD)->count())->toBe(40)
        ->and(RaffleNumber::query()->where('status', RaffleNumber::STATUS_AVAILABLE)->count())->toBe(60)
        ->and(RaffleNumber::query()->whereNull('buyer_name')->count())->toBe(60)
        ->and(RaffleNumber::query()->whereNull('buyer_phone')->count())->toBe(60);
});

it('can be rerun without duplicating raffle numbers', function () {
    $this->seed(RaffleSeeder::class);
    $this->seed(RaffleSeeder::class);

    expect(Raffle::query()->count())->toBe(1)
        ->and(RaffleNumber::query()->count())->toBe(100)
        ->and(RaffleNumber::query()->where('status', RaffleNumber::STATUS_SOLD)->count())->toBe(40);
});
