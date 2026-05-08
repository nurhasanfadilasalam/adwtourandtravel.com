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
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->onDelete('cascade');

            $table->string('document_type');     // ktp, kk, passport, photo, etc.
            $table->string('document_number')->nullable();
            $table->string('issued_city')->nullable();

            $table->date('issued_at')->nullable();
            $table->date('expired_at')->nullable();

            $table->string('file_path'); // lokasi file dokumen

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
    }
};
