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
            $table->string('file_sk')->nullable()->after('saldo_awal');
            $table->string('nomor_sk')->nullable()->after('file_sk');
            $table->date('tanggal_sk')->nullable()->after('nomor_sk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['file_sk', 'nomor_sk', 'tanggal_sk']);
        });
    }
};
