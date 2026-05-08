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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
             // RELATIONS
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // MAIN FIELDS
            $table->decimal('jumlah_bayar', 15, 2)->default(0);
            $table->date('tanggal_bayar');

            $table->enum('metode_pembayaran', [
                'cash',
                'transfer',
            ])->default('cash');

            $table->string('bukti_bayar')->nullable(); // path ke file

            $table->enum('status', [
                'pending',      // menunggu verifikasi
                'verified',     // sudah diverifikasi admin
                'rejected',     // ditolak
            ])->default('pending');
            // USER CREATOR (Admin / Staff)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
