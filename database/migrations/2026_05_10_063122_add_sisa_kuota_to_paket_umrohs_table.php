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
        Schema::table('paket_umrohs', function (Blueprint $table) {
            $table->integer('sisa_kuota')->default(0)->after('kuota');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_umrohs', function (Blueprint $table) {
            $table->dropColumn(['sisa_kuota']);
        });
    }
};
