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
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->string('kategori', 50)->default('kegiatan_kemahasiswaan')->after('isi');
            $table->string('status', 30)->default('published')->after('kategori')->index();
            $table->foreignId('disetujui_oleh_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->text('catatan_kurasi')->nullable()->after('disetujui_oleh_id');
            $table->date('tanggal_kegiatan')->nullable()->after('catatan_kurasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropForeign(['disetujui_oleh_id']);
            $table->dropColumn([
                'kategori',
                'status',
                'disetujui_oleh_id',
                'catatan_kurasi',
                'tanggal_kegiatan',
            ]);
        });
    }
};
