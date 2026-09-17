<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BR-10: penanda apakah suatu barang inventaris boleh dibawa keluar kampus.
     */
    public function up(): void
    {
        Schema::table('master_barang', function (Blueprint $table) {
            $table->boolean('boleh_dibawa_keluar')->default(true)->after('status_aktif');
        });
    }

    public function down(): void
    {
        Schema::table('master_barang', function (Blueprint $table) {
            $table->dropColumn('boleh_dibawa_keluar');
        });
    }
};
