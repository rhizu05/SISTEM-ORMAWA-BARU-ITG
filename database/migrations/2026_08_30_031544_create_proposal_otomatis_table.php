<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_otomatis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->text('latar_belakang')->nullable();
            $table->text('tujuan')->nullable();
            $table->string('sasaran')->nullable();
            $table->text('penutup')->nullable();
            
            // Penandatangan 1
            $table->string('ttd_1_role')->default('ketua'); // ketua, sekretaris, bendahara, custom
            $table->string('ttd_1_nama')->nullable();
            $table->string('ttd_1_jabatan')->nullable();
            $table->string('ttd_1_nim')->nullable();
            $table->string('ttd_1_file')->nullable();
            
            // Penandatangan 2
            $table->string('ttd_2_role')->default('none');
            $table->string('ttd_2_nama')->nullable();
            $table->string('ttd_2_jabatan')->nullable();
            $table->string('ttd_2_nim')->nullable();
            $table->string('ttd_2_file')->nullable();
            
            // Penandatangan 3 (Mengetahui)
            $table->string('ttd_3_role')->default('none');
            $table->string('ttd_3_nama')->nullable();
            $table->string('ttd_3_jabatan')->nullable();
            $table->string('ttd_3_nim')->nullable();
            $table->string('ttd_3_file')->nullable();
            
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_otomatis');
    }
};
