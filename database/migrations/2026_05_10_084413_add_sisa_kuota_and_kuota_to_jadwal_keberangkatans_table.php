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
        Schema::table('jadwal_keberangkatans', function (Blueprint $table) {
            $table->integer('kuota')->default(0)->after('tanggal_kembali');
            $table->integer('sisa_kuota')->default(0)->after('kuota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_keberangkatans', function (Blueprint $table) {
            $table->dropColumn(['kuota', 'sisa_kuota']);
        });
    }
};
