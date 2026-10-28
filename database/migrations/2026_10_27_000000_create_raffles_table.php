<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('raffle_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number');
            $table->string('status')->default('available');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->timestamps();

            $table->unique(['raffle_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raffle_numbers');
        Schema::dropIfExists('raffles');
    }
};
