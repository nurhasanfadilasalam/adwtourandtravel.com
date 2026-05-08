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
        Schema::create('hotel_madinahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hotel', 200);
            $table->string('bintang_hotel', 200);
            $table->string('tarif_hotel_double_room', 200)->nullable();
            $table->string('tarif_hotel_triple_room', 200)->nullable();
            $table->string('tarif_hotel_suite_room', 200)->nullable();
            $table->string('contact_sales_hotel', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_madinahs');
    }
};
