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
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('hotel_id')->after('id')->constrained()->onDelete('cascade');
            
            // Index untuk performa
            $table->index('hotel_id');
            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'room_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['hotel_id']);
            $table->dropIndex(['hotel_id']);
            $table->dropIndex(['hotel_id', 'status']);
            $table->dropIndex(['hotel_id', 'room_type']);
            $table->dropColumn('hotel_id');
        });
    }
};
