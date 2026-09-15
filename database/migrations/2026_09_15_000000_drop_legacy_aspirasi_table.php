<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // BASE-03: hapus tabel aspirasi legacy (skema lama) bila masih ada.
        // Skema aktif adalah `aspirasis` (lihat create_aspirasis_table).
        Schema::dropIfExists('aspirasi');
    }

    public function down(): void
    {
        // Tidak dibuat ulang: tabel legacy tidak dipakai aplikasi.
    }
};
