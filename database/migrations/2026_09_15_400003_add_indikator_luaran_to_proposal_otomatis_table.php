<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRD §19 (Data & Dokumen): indikator & luaran (tujuan → indikator → luaran → dampak).
     */
    public function up(): void
    {
        Schema::table('proposal_otomatis', function (Blueprint $table) {
            $table->text('indikator')->nullable()->after('sasaran');
            $table->text('luaran')->nullable()->after('indikator');
            $table->text('dampak')->nullable()->after('luaran');
        });
    }

    public function down(): void
    {
        Schema::table('proposal_otomatis', function (Blueprint $table) {
            $table->dropColumn(['indikator', 'luaran', 'dampak']);
        });
    }
};
