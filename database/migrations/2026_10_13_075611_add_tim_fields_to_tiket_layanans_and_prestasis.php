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
            $table->string('jenis_kepesertaan', 20)->default('individu')->after('sub_kategori')->index();
            $table->string('nama_tim')->nullable()->after('jenis_kepesertaan');
            $table->string('dosen_pembimbing')->nullable()->after('nama_tim');
            $table->json('anggota_tim')->nullable()->after('dosen_pembimbing');
        });

        Schema::table('prestasis', function (Blueprint $table) {
            $table->string('jenis_kepesertaan', 20)->default('individu')->after('user_id')->index();
            $table->string('nama_tim')->nullable()->after('jenis_kepesertaan');
            $table->string('dosen_pembimbing')->nullable()->after('nama_tim');
            $table->json('anggota_tim')->nullable()->after('dosen_pembimbing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket_layanans', function (Blueprint $table) {
            $table->dropColumn(['jenis_kepesertaan', 'nama_tim', 'dosen_pembimbing', 'anggota_tim']);
        });

        Schema::table('prestasis', function (Blueprint $table) {
            $table->dropColumn(['jenis_kepesertaan', 'nama_tim', 'dosen_pembimbing', 'anggota_tim']);
        });
    }
};
