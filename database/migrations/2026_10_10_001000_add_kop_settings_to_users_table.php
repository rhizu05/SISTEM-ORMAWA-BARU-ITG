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
            if (! Schema::hasColumn('users', 'nama_kop')) {
                $table->string('nama_kop')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'email_kop')) {
                $table->string('email_kop')->nullable()->after('website');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = array_filter(['nama_kop', 'email_kop'], fn ($col) => Schema::hasColumn('users', $col));
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
