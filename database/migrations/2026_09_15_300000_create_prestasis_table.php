<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-020: pelaporan prestasi/kompetisi (termasuk individual/non-afiliasi).
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->string('penyelenggara')->nullable();
            $table->enum('tingkat', ['Fakultas', 'Universitas', 'Regional', 'Nasional', 'Internasional'])->default('Universitas');
            $table->string('juara')->nullable();
            $table->date('tanggal')->nullable();
            $table->enum('afiliasi', ['individu', 'ormawa', 'bem', 'ukm', 'lainnya'])->default('individu');
            $table->string('unit_terkait')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_bukti')->nullable();
            $table->enum('status', ['pending', 'terverifikasi', 'ditolak'])->default('pending');
            $table->text('catatan_bkhm')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};
