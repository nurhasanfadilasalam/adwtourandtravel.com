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
        Schema::create('tour_leaders', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tour_leader');
            $table->string('nik')->unique();
            $table->string('no_passport')->nullable();
            $table->string('nomor_visa')->nullable();
            $table->date('tgl_awal_visa')->nullable();
            $table->date('tgl_akhir_visa')->nullable();
            $table->unsignedBigInteger('id_karyawan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('nomor_handphone');
            $table->string('email')->unique();
            $table->text('alamat')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_leaders');
    }
};
