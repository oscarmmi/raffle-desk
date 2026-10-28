<?php

namespace Tests\Feature;

use App\Models\RaffleNumber;
use App\Models\User;
use Database\Seeders\RaffleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicRaffleGridTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_grid_shows_one_hundred_numbers_without_buyer_details(): void
    {
        $this->seed(RaffleSeeder::class);

        RaffleNumber::query()->where('number', 3)->update([
            'status' => RaffleNumber::STATUS_SOLD,
            'buyer_name' => 'Private Buyer Name',
            'buyer_phone' => '3001234567',
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Raffles/Show')
                ->where('is_owner', false))
            ->assertDontSee('Private Buyer Name')
            ->assertDontSee('3001234567');

        $numbers = $response->viewData('page')['props']['raffle']['numbers'];

        $this->assertCount(100, $numbers);
        $this->assertSame('03', $numbers[3]['display_number']);
        $this->assertArrayNotHasKey('buyer_name', $numbers[3]);
        $this->assertArrayNotHasKey('buyer_phone', $numbers[3]);
    }

    public function test_authenticated_owner_receives_buyer_details(): void
    {
        $this->seed(RaffleSeeder::class);

        RaffleNumber::query()->where('number', 3)->update([
            'status' => RaffleNumber::STATUS_SOLD,
            'buyer_name' => 'Private Buyer Name',
            'buyer_phone' => '3001234567',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Raffles/Show')
                ->where('is_owner', true)
                ->where('raffle.numbers.3.buyer_name', 'Private Buyer Name')
                ->where('raffle.numbers.3.buyer_phone', '3001234567'));
    }
}
