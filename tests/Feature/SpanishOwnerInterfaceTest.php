<?php

use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the owner login as an Inertia page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

it('returns Spanish validation messages when buyer details are missing', function () {
    $raffle = Raffle::query()->create(['name' => 'Test raffle']);
    $number = $raffle->numbers()->create([
        'number' => 9,
        'status' => RaffleNumber::STATUS_AVAILABLE,
    ]);

    $this->actingAs(User::factory()->create())
        ->patchJson(route('owner.numbers.sell', $number), [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.buyer_name.0', 'El campo nombre del comprador es obligatorio.')
        ->assertJsonPath('errors.buyer_phone.0', 'El campo teléfono es obligatorio.');
});
