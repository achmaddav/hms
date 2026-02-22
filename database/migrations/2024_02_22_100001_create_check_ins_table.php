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
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            
            // Check-in Number
            $table->string('checkin_number')->unique(); // CHK-20260222-001
            
            // Guest Information
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone');
            $table->string('guest_id_card')->nullable(); // KTP/Passport
            $table->text('guest_address')->nullable();
            
            // Stay Details
            $table->dateTime('check_in_date'); // Tanggal & jam check-in
            $table->dateTime('check_out_date')->nullable(); // Tanggal & jam check-out (null jika belum checkout)
            $table->integer('duration_days')->default(1); // Estimasi lama menginap (hari)
            $table->integer('guests')->default(1); // Jumlah tamu
            
            // Pricing
            $table->decimal('room_price', 12, 2); // Harga per malam
            $table->decimal('total_nights', 8, 2)->default(1); // Total malam (bisa decimal untuk checkout lebih cepat)
            $table->decimal('room_total', 12, 2); // room_price * total_nights
            $table->decimal('additional_charges', 12, 2)->default(0); // Biaya tambahan (minibar, laundry, dll)
            $table->decimal('tax', 12, 2)->default(0); // Pajak
            $table->decimal('discount', 12, 2)->default(0); // Diskon
            $table->decimal('total_amount', 12, 2); // Total yang harus dibayar
            $table->decimal('paid_amount', 12, 2)->default(0); // Yang sudah dibayar
            $table->decimal('remaining_amount', 12, 2)->default(0); // Sisa yang harus dibayar
            
            // Payment Status
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            
            // Status
            $table->enum('status', [
                'checked_in',   // Sedang menginap
                'checked_out',  // Sudah check-out
            ])->default('checked_in');
            
            // Staff tracking
            $table->foreignId('checked_in_by')->constrained('users'); // Resepsionis yang handle check-in
            $table->foreignId('checked_out_by')->nullable()->constrained('users'); // Resepsionis yang handle check-out
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('hotel_id');
            $table->index('room_id');
            $table->index('checkin_number');
            $table->index('status');
            $table->index('check_in_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
