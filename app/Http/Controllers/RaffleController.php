<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\RaffleNumber;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RaffleController extends Controller
{
    public function __invoke(): Response
    {
        $raffle = Raffle::query()->with(['numbers', 'winnerNumber'])->firstOrFail();
        $isOwner = Auth::check();

        return Inertia::render('Raffles/Show', [
            'raffle' => [
                'name' => $raffle->name,
                'draw' => [
                    'drawn_at' => $raffle->drawn_at?->toIso8601String(),
                    'winner_number' => $raffle->winnerNumber?->displayNumber(),
                ],
                'numbers' => $raffle->numbers->map(fn (RaffleNumber $number): array => [
                    'id' => $number->id,
                    'number' => $number->number,
                    'display_number' => $number->displayNumber(),
                    'status' => $number->status,
                    ...($isOwner ? [
                        'buyer_name' => $number->buyer_name,
                        'buyer_phone' => $number->buyer_phone,
                    ] : []),
                ])->values(),
            ],
            'is_owner' => $isOwner,
        ]);
    }
}
