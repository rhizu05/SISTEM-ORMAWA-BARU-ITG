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
            if (! Schema::hasColumn('users', 'prodi')) {
                $table->string('prodi')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('alamat');
            }
            if (! Schema::hasColumn('users', 'periode_kepengurusan')) {
                $table->string('periode_kepengurusan')->nullable()->after('prodi');
            }
        });

        Schema::table('letters', function (Blueprint $table) {
            if (! Schema::hasColumn('letters', 'lampiran')) {
                $table->string('lampiran')->nullable()->after('perihal');
            }
            if (! Schema::hasColumn('letters', 'file_lampiran_pdf')) {
                $table->string('file_lampiran_pdf')->nullable()->after('content');
            }
            if (! Schema::hasColumn('letters', 'peminjaman_id')) {
                $table->unsignedBigInteger('peminjaman_id')->nullable()->after('proposal_otomatis_id');
                $table->string('peminjaman_type')->nullable()->after('peminjaman_id');
                $table->index(['peminjaman_type', 'peminjaman_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            if (Schema::hasColumn('letters', 'peminjaman_id')) {
                $table->dropIndex(['peminjaman_type', 'peminjaman_id']);
                $table->dropColumn(['peminjaman_id', 'peminjaman_type']);
            }
            if (Schema::hasColumn('letters', 'file_lampiran_pdf')) {
                $table->dropColumn('file_lampiran_pdf');
            }
            if (Schema::hasColumn('letters', 'lampiran')) {
                $table->dropColumn('lampiran');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $colsToDrop = array_filter(['prodi', 'website', 'periode_kepengurusan'], fn ($col) => Schema::hasColumn('users', $col));
            if (! empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
