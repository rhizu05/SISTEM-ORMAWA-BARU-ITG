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
            $table->decimal('saldo_awal', 15, 2)->default(0)->after('saldo');
        });
        // backfill existing saldo to saldo_awal
        \Illuminate\Support\Facades\DB::statement('UPDATE users SET saldo_awal = saldo WHERE saldo_awal = 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('saldo_awal');
        });
    }
};
