<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->enum('room_type', ['standard', 'deluxe', 'suite', 'presidential']);
            $table->decimal('price_per_night', 10, 2);
            $table->integer('capacity'); // Jumlah orang
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // Fasilitas: AC, TV, WiFi, dll
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->integer('floor')->nullable();
            $table->decimal('size', 8, 2)->nullable(); // Ukuran dalam m2
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
