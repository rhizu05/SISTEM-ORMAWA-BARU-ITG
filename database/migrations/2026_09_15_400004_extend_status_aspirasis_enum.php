<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * PRD §18: lengkapi status aspirasi (direkap, ditindaklanjuti, tidak terbukti)
     * dan sekaligus perbaiki status 'ditolak' yang belum ada di enum.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE aspirasis MODIFY status ENUM('pending','direkap','diproses','ditindaklanjuti','selesai','ditolak','tidak_terbukti') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE aspirasis MODIFY status ENUM('pending','diproses','selesai') NOT NULL DEFAULT 'pending'");
        }
    }
};
