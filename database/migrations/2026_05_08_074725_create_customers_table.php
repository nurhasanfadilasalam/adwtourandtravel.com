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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('no_ktp')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('no_passport')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('kota_passport')->nullable();
            $table->date('tgl_dikeluarkan_passport')->nullable();
            $table->date('tgl_habis_passport')->nullable();

            $table->string('nama_ktp')->nullable();
            $table->string('nama_passport')->nullable();
            $table->text('alamat')->nullable();

            $table->date('tgl_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('kewarganegaraan')->nullable();

            $table->string('status_pernikahan')->nullable();
            $table->string('jenis_pendidikan')->nullable();
            $table->string('jenis_pekerjaan')->nullable();
            $table->string('no_hp')->nullable();

            $table->string('upload_ktp')->nullable();
            $table->string('upload_kk')->nullable();
            $table->string('upload_passport')->nullable();
            $table->string('upload_photo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
