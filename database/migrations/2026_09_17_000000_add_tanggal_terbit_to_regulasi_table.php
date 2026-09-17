<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BACKLOG-004 (B2): form, controller, dan view regulasi memakai `tanggal_terbit`,
 * tetapi kolomnya hanya ada di tabel legacy `regulasis` (plural, tidak dipakai
 * aplikasi) — sehingga nilainya dibuang diam-diam oleh Eloquent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->date('tanggal_terbit')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->dropColumn('tanggal_terbit');
        });
    }
};
