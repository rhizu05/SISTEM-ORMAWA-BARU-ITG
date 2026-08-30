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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif')->after('password');
            $table->decimal('saldo', 15, 2)->default(0)->after('status_akun');
            $table->string('foto_profil')->nullable();
            $table->string('logo_ormawa')->nullable();
            $table->string('nama_ketua')->nullable();
            $table->string('nama_sekretaris')->nullable();
            $table->string('nama_bendahara')->nullable();
            $table->string('ttd_ketua')->nullable();
            $table->string('ttd_sekretaris')->nullable();
            $table->string('ttd_bendahara')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'status_akun', 'saldo', 'foto_profil', 'logo_ormawa',
                'nama_ketua', 'nama_sekretaris', 'nama_bendahara',
                'ttd_ketua', 'ttd_sekretaris', 'ttd_bendahara',
                'alamat', 'telepon'
            ]);
        });
    }
};
