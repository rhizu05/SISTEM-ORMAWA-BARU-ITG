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
        Schema::create('surat_peringatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_user_id')->constrained('users')->onDelete('cascade'); // Ormawa
            $table->string('nomor_surat')->unique();
            $table->enum('tingkat', ['SP-1','SP-2','SP-3'])->default('SP-1');
            $table->string('perihal');
            $table->string('alasan_singkat');
            $table->text('deskripsi');
            $table->text('sanksi');
            $table->date('tanggal_surat');
            $table->string('penandatangan');
            $table->string('pdf_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_peringatans');
    }
};
