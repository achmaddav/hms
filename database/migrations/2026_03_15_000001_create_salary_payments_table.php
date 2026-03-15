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
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            
            // Payment Info
            $table->string('payment_number')->unique(); // SAL-202603-001
            $table->string('month_year'); // Format: 2026-03
            
            // Salary Components
            $table->decimal('base_salary', 12, 2);
            $table->decimal('allowances', 12, 2)->default(0); // Tunjangan
            $table->decimal('overtime', 12, 2)->default(0); // Lembur
            $table->decimal('bonus', 12, 2)->default(0);
            
            // Deductions
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('insurance', 12, 2)->default(0); // BPJS
            $table->decimal('other_deductions', 12, 2)->default(0);
            
            // Totals
            $table->decimal('gross_salary', 12, 2); // base + allowances + overtime + bonus
            $table->decimal('total_deductions', 12, 2); // tax + insurance + other
            $table->decimal('net_salary', 12, 2); // gross - deductions
            
            // Payment Details
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check'])->nullable();
            $table->string('bank_account')->nullable();
            $table->string('reference_number')->nullable(); // Nomor transfer
            
            // Working Hours (for overtime calculation)
            $table->integer('working_days')->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            
            $table->text('notes')->nullable();
            
            // Staff tracking
            $table->foreignId('processed_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // Indexes
            $table->index('hotel_id');
            $table->index('employee_id');
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
        Schema::dropIfExists('salary_payments');
    }
};
