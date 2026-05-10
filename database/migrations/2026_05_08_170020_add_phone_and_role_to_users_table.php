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
            // Menambahkan kolom phone (nullable jika tidak wajib diisi saat daftar)
            $table->string('phone')->nullable()->after('password');

            // Menambahkan kolom role setelah phone
            $table->enum('role', ['admin', 'staff', 'customer', 'administrator'])
                  ->default('customer')
                  ->after('phone');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom yang sudah dibuat jika di-rollback
            $table->dropColumn(['phone', 'role']);
        });
    }
};
