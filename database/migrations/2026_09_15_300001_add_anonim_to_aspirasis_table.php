<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-015: tandai aspirasi anonim (identitas tetap disimpan di user_id).
        Schema::table('aspirasis', function (Blueprint $table) {
            if (! Schema::hasColumn('aspirasis', 'anonim')) {
                $table->boolean('anonim')->default(false)->after('kategori');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aspirasis', function (Blueprint $table) {
            if (Schema::hasColumn('aspirasis', 'anonim')) {
                $table->dropColumn('anonim');
            }
        });
    }
};
