<?php

use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\User;
use Database\Seeders\RaffleSeeder;

beforeEach(function () {
    $this->seed(RaffleSeeder::class);
});

it('allows an owner to sell a raffle number', function () {
    $number = RaffleNumber::query()->where('number', 6)->firstOrFail();

    $this->actingAs(User::factory()->create())
        ->patch(route('owner.numbers.sell', $number), [
            'buyer_name' => 'Marta Ruiz',
            'buyer_phone' => '3005550182',
        ])
        ->assertRedirect();

    expect($number->fresh()->status)->toBe(RaffleNumber::STATUS_SOLD)
        ->and($number->fresh()->buyer_name)->toBe('Marta Ruiz')
        ->and($number->fresh()->buyer_phone)->toBe('3005550182');
});

it('returns 422 when a sold number is sold again', function () {
    $number = RaffleNumber::query()->where('number', 6)->firstOrFail();
    $owner = User::factory()->create();

    $this->actingAs($owner)->patch(route('owner.numbers.sell', $number), [
        'buyer_name' => 'Marta Ruiz',
        'buyer_phone' => '3005550182',
    ])->assertRedirect();

    $this->actingAs($owner)->patchJson(route('owner.numbers.sell', $number), [
        'buyer_name' => 'Someone Else',
        'buyer_phone' => '3000000000',
    ])->assertUnprocessable();

    expect($number->fresh()->buyer_name)->toBe('Marta Ruiz');
});

it('draws and stores a winner from sold numbers only', function () {
    $raffle = Raffle::query()->firstOrFail();
    $raffle->numbers()->update([
        'status' => RaffleNumber::STATUS_AVAILABLE,
        'buyer_name' => null,
        'buyer_phone' => null,
    ]);
    $soldNumbers = RaffleNumber::query()->whereIn('number', [2, 18, 42])->get();
    $soldNumbers->each(fn (RaffleNumber $number) => $number->update([
        'status' => RaffleNumber::STATUS_SOLD,
        'buyer_name' => 'Demo buyer '.$number->displayNumber(),
        'buyer_phone' => '3001234567',
    ]));

    $this->actingAs(User::factory()->create())
        ->post(route('owner.draw'))
        ->assertRedirect();

    $raffle->refresh();

    expect($raffle->winnerNumber)->not->toBeNull()
        ->and($raffle->winnerNumber->status)->toBe(RaffleNumber::STATUS_SOLD)
        ->and($soldNumbers->modelKeys())->toContain($raffle->winner_number_id)
        ->and($raffle->draw_seed)->toMatch('/^[a-f0-9]{64}$/')
        ->and($raffle->drawn_at)->not->toBeNull();
});

it('returns 422 rather than drawing a second time', function () {
    $raffle = Raffle::query()->firstOrFail();
    $raffle->numbers()->update([
        'status' => RaffleNumber::STATUS_AVAILABLE,
        'buyer_name' => null,
        'buyer_phone' => null,
    ]);
    $number = RaffleNumber::query()->where('number', 18)->firstOrFail();
    $number->update([
        'status' => RaffleNumber::STATUS_SOLD,
        'buyer_name' => 'Winner',
        'buyer_phone' => '3001234567',
    ]);

    $owner = User::factory()->create();
    $this->actingAs($owner)->post(route('owner.draw'))->assertRedirect();
    $raffle->refresh();
    $winnerId = $raffle->winner_number_id;
    $seed = $raffle->draw_seed;

    $this->actingAs($owner)->postJson(route('owner.draw'))->assertUnprocessable();

    expect($raffle->fresh()->winner_number_id)->toBe($winnerId)
        ->and($raffle->fresh()->draw_seed)->toBe($seed);
});
