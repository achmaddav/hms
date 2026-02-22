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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check_ins')->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            
            // Payment Details
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', [
                'cash',
                'credit_card',
                'debit_card',
                'bank_transfer',
                'qris',
                'e_wallet'
            ]);
            
            // Payment Info
            $table->string('card_number')->nullable(); // 4 digit terakhir untuk kartu
            $table->string('transaction_id')->nullable(); // ID transaksi dari payment gateway
            $table->text('notes')->nullable();
            
            // Staff who processed
            $table->foreignId('processed_by')->constrained('users');
            
            $table->timestamps();
            
            $table->index('check_in_id');
            $table->index('hotel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
