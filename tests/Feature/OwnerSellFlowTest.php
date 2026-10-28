<?php

namespace Tests\Feature;

use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerSellFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_sign_in_and_sign_out(): void
    {
        $owner = User::factory()->create([
            'password' => 'secret-password',
        ]);

        $this->post(route('login.store'), [
            'email' => $owner->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($owner);

        $this->post(route('logout'))->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_guests_cannot_sell_raffle_numbers(): void
    {
        $number = $this->createNumber();

        $this->patch(route('owner.numbers.sell', $number), [
            'buyer_name' => 'Ana Perez',
            'buyer_phone' => '3001234567',
        ])->assertRedirect(route('login'));

        $this->assertSame(RaffleNumber::STATUS_AVAILABLE, $number->fresh()->status);
    }

    public function test_owner_can_sell_an_available_number_with_buyer_details(): void
    {
        $number = $this->createNumber();

        $this->actingAs(User::factory()->create())
            ->patch(route('owner.numbers.sell', $number), [
                'buyer_name' => 'Ana Perez',
                'buyer_phone' => '3001234567',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('raffle_numbers', [
            'id' => $number->id,
            'status' => RaffleNumber::STATUS_SOLD,
            'buyer_name' => 'Ana Perez',
            'buyer_phone' => '3001234567',
        ]);
    }

    public function test_selling_a_number_twice_returns_unprocessable_entity(): void
    {
        $number = $this->createNumber();
        $owner = User::factory()->create();

        $this->actingAs($owner)->patchJson(route('owner.numbers.sell', $number), [
            'buyer_name' => 'Ana Perez',
            'buyer_phone' => '3001234567',
        ])->assertRedirect();

        $this->actingAs($owner)->patchJson(route('owner.numbers.sell', $number), [
            'buyer_name' => 'Luis Gomez',
            'buyer_phone' => '3107654321',
        ])->assertUnprocessable();

        $this->assertDatabaseHas('raffle_numbers', [
            'id' => $number->id,
            'status' => RaffleNumber::STATUS_SOLD,
            'buyer_name' => 'Ana Perez',
            'buyer_phone' => '3001234567',
        ]);
    }

    private function createNumber(): RaffleNumber
    {
        $raffle = Raffle::query()->create(['name' => 'Test raffle']);

        return $raffle->numbers()->create([
            'number' => 7,
            'status' => RaffleNumber::STATUS_AVAILABLE,
        ]);
    }
}
