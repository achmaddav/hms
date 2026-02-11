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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['room_service', 'spa', 'laundry', 'restaurant', 'transportation', 'other']);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('icon')->nullable(); // Icon name atau path
            $table->boolean('is_active')->default(true);
            $table->string('duration')->nullable(); // Contoh: "30 minutes", "1 hour"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
