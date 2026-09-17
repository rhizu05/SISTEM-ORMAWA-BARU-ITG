<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRD §19: tiap perubahan saldo tercatat pada periode anggaran berjalan.
     */
    public function up(): void
    {
        Schema::table('saldo_histori', function (Blueprint $table) {
            $table->foreignId('periode_anggaran_id')
                ->nullable()
                ->after('actor_id')
                ->constrained('periode_anggarans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('saldo_histori', function (Blueprint $table) {
            $table->dropConstrainedForeignId('periode_anggaran_id');
        });
    }
};
