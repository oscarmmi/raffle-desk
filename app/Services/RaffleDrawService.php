<?php

namespace App\Services;

use App\Models\Raffle;
use App\Models\RaffleNumber;
use Illuminate\Support\Facades\DB;

class RaffleDrawService
{
    public function draw(Raffle $raffle): RaffleNumber
    {
        return DB::transaction(function () use ($raffle): RaffleNumber {
            $lockedRaffle = Raffle::query()
                ->whereKey($raffle->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRaffle->drawn_at !== null) {
                abort(422, 'El sorteo ya se realizó.');
            }

            $soldNumbers = $lockedRaffle->numbers()
                ->where('status', RaffleNumber::STATUS_SOLD)
                ->orderBy('number')
                ->lockForUpdate()
                ->get();

            if ($soldNumbers->isEmpty()) {
                abort(422, 'No hay números vendidos para realizar el sorteo.');
            }

            $seed = bin2hex(random_bytes(32));
            $winner = $soldNumbers[$this->indexForSeed($seed, $soldNumbers->count())];

            $lockedRaffle->forceFill([
                'winner_number_id' => $winner->id,
                'draw_seed' => $seed,
                'drawn_at' => now(),
            ])->save();

            return $winner;
        });
    }

    private function indexForSeed(string $seed, int $numberOfOptions): int
    {
        $range = 4_294_967_296;
        $limit = intdiv($range, $numberOfOptions) * $numberOfOptions;

        for ($counter = 0; ; $counter++) {
            $candidate = hexdec(substr(hash('sha256', $seed.':'.$counter), 0, 8));

            if ($candidate < $limit) {
                return $candidate % $numberOfOptions;
            }
        }
    }
}
