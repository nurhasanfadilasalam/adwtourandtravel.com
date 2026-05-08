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
        Schema::create('paket_umrohs', function (Blueprint $table) {
            $table->id();
                // MAIN INFO
            $table->string('nama_paket');
            $table->text('deskripsi')->nullable();

            $table->integer('durasi_hari')->default(0);
            $table->integer('kuota')->default(0);
            $table->decimal('harga_paket', 15, 2)->default(0);

            // RELATIONS
            $table->foreignId('hotel_mekah_id')
                ->nullable()
                ->constrained('hotel_mekahs')
                ->nullOnDelete();

            $table->foreignId('hotel_madinah_id')
                ->nullable()
                ->constrained('hotel_madinahs')
                ->nullOnDelete();

            // DATE FIELDS
            $table->date('tanggal_start')->nullable();
            $table->date('tanggal_end')->nullable();

            // EXTRA INFO
            $table->longText('include')->nullable();
            $table->longText('exclude')->nullable();
            $table->longText('syarat')->nullable();

            $table->string('thumbnail')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_umrohs');
    }
};
