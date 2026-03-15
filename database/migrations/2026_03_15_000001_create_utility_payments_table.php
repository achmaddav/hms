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
        Schema::create('utility_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            
            // Payment Info
            $table->string('payment_number')->unique(); // UTL-202603-001
            $table->enum('utility_type', ['electricity', 'water', 'gas', 'internet', 'maintenance', 'other']);
            $table->string('month_year'); // Format: 2026-03 (untuk periode billing)
            
            // Meter Readings (optional, for electricity/water)
            $table->decimal('previous_reading', 10, 2)->nullable();
            $table->decimal('current_reading', 10, 2)->nullable();
            $table->decimal('usage', 10, 2)->nullable(); // current - previous
            
            // Pricing
            $table->decimal('rate_per_unit', 10, 2)->default(0); // Harga per kWh/m3
            $table->decimal('base_charge', 12, 2)->default(0); // Biaya tetap bulanan
            $table->decimal('usage_charge', 12, 2)->default(0); // usage * rate_per_unit
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            
            // Payment Details
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'auto_debit'])->nullable();
            
            // References
            $table->string('bill_reference')->nullable(); // Nomor tagihan dari PLN/PDAM
            $table->text('notes')->nullable();
            
            // Staff tracking
            $table->foreignId('recorded_by')->constrained('users');
            $table->foreignId('paid_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // Indexes
            $table->index('hotel_id');
            $table->index('room_id');
            $table->index('payment_number');
            $table->index('status');
            $table->index('month_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_payments');
    }
};
