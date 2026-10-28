<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->foreignId('winner_number_id')
                ->nullable()
                ->constrained('raffle_numbers')
                ->nullOnDelete();
            $table->char('draw_seed', 64)->nullable();
            $table->timestamp('drawn_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('winner_number_id');
            $table->dropColumn(['draw_seed', 'drawn_at']);
        });
    }
};
