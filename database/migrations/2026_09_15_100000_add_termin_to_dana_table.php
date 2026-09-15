<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dana', function (Blueprint $table) {
            $table->unsignedInteger('termin_ke')->default(1)->after('pengajuan_id');
            $table->text('catatan')->nullable()->after('tanggal_cair');
        });

        // Backfill: pencairan lama dianggap termin ke-1
        DB::table('dana')->whereNull('termin_ke')->update(['termin_ke' => 1]);
    }

    public function down(): void
    {
        Schema::table('dana', function (Blueprint $table) {
            $table->dropColumn(['termin_ke', 'catatan']);
        });
    }
};
