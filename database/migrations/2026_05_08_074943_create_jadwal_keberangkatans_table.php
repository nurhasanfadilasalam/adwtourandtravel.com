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
        Schema::create('jadwal_keberangkatans', function (Blueprint $table) {
            $table->id();
            // RELATIONS
            $table->foreignId('paket_umroh_id')
                ->constrained('paket_umrohs')
                ->cascadeOnDelete();
                // ->nullOnDelete();

            $table->foreignId('tour_leader_id')
                ->nullable()
                ->constrained('tour_leaders')
                ->nullOnDelete();

            // $table->string('tour_leader_name')->nullable();

            $table->foreignId('muthawif_id')
                ->nullable()
                ->constrained('muthawifs')
                ->nullOnDelete();

            // $table->string('muthawif_name')->nullable();

            $table->foreignId('maskapai_id')
                ->nullable()
                ->constrained('maskapais')
                ->nullOnDelete();

            // $table->string('maskapai_name')->nullable();

            $table->foreignId('bandara_id')
                ->nullable()
                ->constrained('bandaras')
                ->nullOnDelete();

            // $table->string('bandara_name')->nullable();


            // MAIN FIELDS
            $table->date('tanggal_keberangkatan');
            $table->time('jam_keberangkatan')->nullable();
            $table->date('tanggal_kembali')->nullable();


            $table->enum('status', [
                'draft', // sedang di konsep
                'open',         // bisa di-booking
                'closed',       // ditutup
                'full',         // penuh
                'canceled',     // dibatalkan
            ])->default('draft');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_keberangkatans');
    }
};
