<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BR-11: pencairan termin berikutnya menunggu evaluasi termin sebelumnya
     * dinyatakan memungkinkan.
     */
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->boolean('evaluasi_termin_ok')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('evaluasi_termin_ok');
        });
    }
};
