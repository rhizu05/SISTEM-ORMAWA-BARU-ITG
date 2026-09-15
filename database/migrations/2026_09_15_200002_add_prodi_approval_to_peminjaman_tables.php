<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Q-SAR-04: dokumen persetujuan Prodi (di luar sistem) untuk peminjaman HIMA.
        if (! Schema::hasColumn('peminjaman_tempat', 'file_persetujuan_prodi')) {
            Schema::table('peminjaman_tempat', function (Blueprint $table) {
                $table->string('file_persetujuan_prodi')->nullable()->after('deskripsi_kegiatan');
            });
        }

        if (! Schema::hasColumn('peminjaman_barang', 'file_persetujuan_prodi')) {
            Schema::table('peminjaman_barang', function (Blueprint $table) {
                $table->string('file_persetujuan_prodi')->nullable()->after('kebutuhan_barang');
            });
        }
    }

    public function down(): void
    {
        foreach (['peminjaman_tempat', 'peminjaman_barang'] as $tableName) {
            if (Schema::hasColumn($tableName, 'file_persetujuan_prodi')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('file_persetujuan_prodi');
                });
            }
        }
    }
};
