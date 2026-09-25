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
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->date('tanggal_mulai_kegiatan')->nullable();
            $table->date('tanggal_selesai_kegiatan')->nullable();
            $table->foreignId('program_kerja_id')->nullable()->constrained('program_kerjas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropForeign(['program_kerja_id']);
            $table->dropColumn(['tanggal_mulai_kegiatan', 'tanggal_selesai_kegiatan', 'program_kerja_id']);
        });
    }
};
