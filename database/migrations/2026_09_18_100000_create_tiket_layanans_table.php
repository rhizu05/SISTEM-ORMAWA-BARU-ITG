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
        Schema::create('tiket_layanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket')->unique()->index(); // e.g. SKIN-TKT-2026-0001
            $table->enum('kategori', ['aspirasi', 'konseling', 'prestasi'])->index();
            $table->string('sub_kategori')->nullable(); // lapor_prestasi / pengajuan_dana_delegasi
            
            // Identitas Mahasiswa Pelapor (Wajib)
            $table->string('nim', 30)->index();
            $table->string('nama_mahasiswa');
            $table->string('email')->index();
            $table->string('no_hp', 25)->nullable();
            $table->string('prodi', 100)->nullable();

            // Atribut Khusus Aspirasi
            $table->string('judul')->nullable();
            $table->text('isi')->nullable();
            $table->string('lampiran')->nullable();
            $table->text('catatan_bpm')->nullable();
            $table->text('catatan_bkhm')->nullable();
            $table->timestamp('diteruskan_ke_bkhm_at')->nullable();

            // Atribut Khusus Konseling (Rahasia BKHM)
            $table->string('topik_konseling', 100)->nullable();
            $table->string('metode_konseling', 50)->nullable();
            $table->text('deskripsi_masalah')->nullable();
            $table->text('tanggapan_bkhm')->nullable();
            $table->dateTime('jadwal_temu')->nullable();
            $table->string('lokasi_atau_link')->nullable();

            // Atribut Khusus Prestasi / Delegasi Lomba
            $table->string('nama_kegiatan')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('tingkat', 50)->nullable(); // Kampus/Wilayah/Nasional/Internasional
            $table->string('capaian', 100)->nullable(); // Juara 1, Finalis, dll
            $table->date('tanggal_kegiatan')->nullable();
            $table->decimal('estimasi_biaya', 15, 2)->nullable();
            $table->string('lampiran_bukti')->nullable();
            $table->boolean('tampil_ke_publik')->default(false)->index();

            // Status Keseluruhan
            $table->string('status', 50)->default('pending')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket_layanans');
    }
};
