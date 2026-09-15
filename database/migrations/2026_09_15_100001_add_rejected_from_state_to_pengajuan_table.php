<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-009: simpan titik penolakan agar revisi diajukan ulang ke tahap yang menolak.
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->foreignId('rejected_from_state_id')
                ->nullable()
                ->after('workflow_state_id')
                ->constrained('workflow_states')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rejected_from_state_id');
        });
    }
};
