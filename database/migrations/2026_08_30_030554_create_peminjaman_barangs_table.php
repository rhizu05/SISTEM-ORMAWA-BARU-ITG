<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            
            // JSON column untuk menyimpan array kebutuhan barang [{"id_barang": 1, "qty": 5}, ...]
            $table->json('kebutuhan_barang');
            
            $table->enum('status_bkkh', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->enum('status_sarpras', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->string('status_akhir')->default('Proses BKKH');
            
            $table->text('catatan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_barang');
    }
};
