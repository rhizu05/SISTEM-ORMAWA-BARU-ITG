<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tiket_layanans', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket_layanans', 'konfirmasi_mahasiswa')) {
                $table->string('konfirmasi_mahasiswa')->nullable()->after('lokasi_atau_link');
            }
            if (!Schema::hasColumn('tiket_layanans', 'catatan_konfirmasi_mahasiswa')) {
                $table->text('catatan_konfirmasi_mahasiswa')->nullable()->after('konfirmasi_mahasiswa');
            }
            if (!Schema::hasColumn('tiket_layanans', 'konfirmasi_at')) {
                $table->timestamp('konfirmasi_at')->nullable()->after('catatan_konfirmasi_mahasiswa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket_layanans', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('tiket_layanans', 'konfirmasi_mahasiswa')) $cols[] = 'konfirmasi_mahasiswa';
            if (Schema::hasColumn('tiket_layanans', 'catatan_konfirmasi_mahasiswa')) $cols[] = 'catatan_konfirmasi_mahasiswa';
            if (Schema::hasColumn('tiket_layanans', 'konfirmasi_at')) $cols[] = 'konfirmasi_at';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
