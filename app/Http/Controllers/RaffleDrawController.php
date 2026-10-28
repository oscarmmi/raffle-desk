<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Services\RaffleDrawService;
use Illuminate\Http\RedirectResponse;

class RaffleDrawController extends Controller
{
    public function __invoke(RaffleDrawService $drawService): RedirectResponse
    {
        $raffle = Raffle::query()->firstOrFail();
        $winner = $drawService->draw($raffle);

        return back()->with('success', 'El número ganador es '.$winner->displayNumber().'.');
    }
}
