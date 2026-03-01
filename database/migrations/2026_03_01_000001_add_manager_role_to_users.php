<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL/MariaDB
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'manager', 'receptionist', 'customer') NOT NULL");
        
        // Jika menggunakan PostgreSQL, gunakan ini:
        // DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        // DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('super_admin', 'admin', 'manager', 'receptionist', 'customer'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke kondisi sebelumnya (tanpa manager)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'receptionist', 'customer') NOT NULL");
    }
};
