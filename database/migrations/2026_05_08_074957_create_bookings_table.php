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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
         // RELATIONS
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('paket_umroh_id')
                ->constrained('paket_umrohs')
                ->cascadeOnDelete();

            $table->foreignId('jadwal_keberangkatan_id')
                ->constrained('jadwal_keberangkatans')
                ->cascadeOnDelete();

            // BOOKING INFO
            $table->string('booking_code')->unique();

            $table->enum('status', [
                'pending',           // baru daftar
                'waiting_payment',   // menunggu pembayaran
                'partial',           // pembayaran sebagian
                'paid',              // lunas
                'canceled',          // dibatalkan
            ])->default('pending');

            $table->decimal('total_price', 15, 2)->default(0);

            $table->enum('metode_pembayaran', [
                'cash',
                'transfer',
                'cicilan',
            ])->nullable();

            // USER CREATOR (Admin / Staff)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
