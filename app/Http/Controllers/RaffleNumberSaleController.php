<?php

namespace App\Http\Controllers;

use App\Models\RaffleNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaffleNumberSaleController extends Controller
{
    public function __invoke(Request $request, RaffleNumber $raffleNumber): RedirectResponse
    {
        $buyer = $request->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_phone' => ['required', 'string', 'max:40'],
        ]);

        DB::transaction(function () use ($raffleNumber, $buyer): void {
            $number = RaffleNumber::query()
                ->whereKey($raffleNumber->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($number->status !== RaffleNumber::STATUS_AVAILABLE) {
                abort(422, 'Este número ya no está disponible.');
            }

            $number->update([
                ...$buyer,
                'status' => RaffleNumber::STATUS_SOLD,
            ]);
        });

        return back()->with('success', 'Número vendido correctamente.');
    }
}
