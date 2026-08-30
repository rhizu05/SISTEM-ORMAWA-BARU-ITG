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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // undangan, tugas, permohonan, keterangan_aktif
            $table->string('nomor_surat')->nullable();
            $table->string('perihal');
            $table->text('content');
            $table->json('metadata')->nullable(); // Stores dynamic fields like 'tujuan', 'masa_berlaku'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
