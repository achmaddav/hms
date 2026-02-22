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
        Schema::create('additional_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check_ins')->onDelete('cascade');
            
            // Charge Details
            $table->string('description'); // e.g., "Minibar - Coca Cola", "Laundry - 2 pcs"
            $table->decimal('amount', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 12, 2); // amount * quantity
            
            // Staff who added
            $table->foreignId('added_by')->constrained('users');
            
            $table->timestamps();
            
            $table->index('check_in_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_charges');
    }
};
