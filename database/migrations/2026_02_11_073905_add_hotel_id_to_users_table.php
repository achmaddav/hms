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
        Schema::table('users', function (Blueprint $table) {
            // Hotel relationship - nullable karena admin super tidak punya hotel
            $table->foreignId('hotel_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            
            // Update role enum untuk menambah super_admin
            $table->enum('role', ['super_admin', 'admin', 'receptionist', 'customer'])->default('customer')->change();
            
            // Index untuk performa query
            $table->index('hotel_id');
            $table->index(['hotel_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hotel_id']);
            $table->dropIndex(['hotel_id']);
            $table->dropIndex(['hotel_id', 'role']);
            $table->dropColumn('hotel_id');
            
            // Revert role enum
            $table->enum('role', ['admin', 'receptionist', 'customer'])->default('customer')->change();
        });
    }
};
