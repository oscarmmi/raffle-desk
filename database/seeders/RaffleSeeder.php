<?php

namespace Database\Seeders;

use App\Models\Raffle;
use Illuminate\Database\Seeder;

class RaffleSeeder extends Seeder
{
    private const SOLD_NUMBERS = [
        0, 3, 5, 7, 9, 12, 14, 16, 18, 21,
        23, 25, 27, 29, 31, 34, 36, 38, 40, 42,
        44, 47, 49, 51, 53, 55, 58, 60, 62, 64,
        66, 68, 71, 74, 77, 82, 86, 91, 95, 99,
    ];

    private const BUYER_NAMES = [
        'Ana María Rojas', 'Carlos Andrés Mejía', 'Lucía Torres', 'Jorge Ramírez',
        'Valentina Castro', 'Diego Herrera', 'Mariana López', 'Santiago Vélez',
        'Camila Giraldo', 'Andrés Muñoz', 'Isabella Ospina', 'Mateo Restrepo',
        'Sara Cárdenas', 'Felipe Gómez', 'Daniela Arias', 'Juan Pablo Gil',
        'Natalia Franco', 'David Salazar', 'Gabriela Marín', 'Sebastián Cano',
        'Laura Quintero', 'Nicolás Jaramillo', 'Paula Montoya', 'Tomás Bedoya',
        'Juliana Vargas', 'Samuel Pineda', 'Manuela Duque', 'Esteban Ortiz',
        'Sofía Cardona', 'Martín Correa', 'Isabel Valencia', 'Emilio Restrepo',
        'María José Calle', 'Simón Zapata', 'Catalina Londoño', 'Alejandro Hoyos',
        'Elena González', 'Jerónimo Mesa', 'Diana Osorio', 'Gabriel Patiño',
    ];

    public function run(): void
    {
        $raffle = Raffle::query()->firstOrCreate(
            ['name' => 'Rifa principal'],
        );

        $raffle->forceFill([
            'winner_number_id' => null,
            'draw_seed' => null,
            'drawn_at' => null,
        ])->save();

        foreach (range(0, 99) as $number) {
            $soldIndex = array_search($number, self::SOLD_NUMBERS, true);
            $isSold = $soldIndex !== false;

            $raffle->numbers()->updateOrCreate(
                ['number' => $number],
                [
                    'status' => $isSold ? 'sold' : 'available',
                    'buyer_name' => $isSold ? self::BUYER_NAMES[$soldIndex] : null,
                    'buyer_phone' => $isSold ? sprintf('300555%04d', $soldIndex + 1) : null,
                ],
            );
        }
    }
}
