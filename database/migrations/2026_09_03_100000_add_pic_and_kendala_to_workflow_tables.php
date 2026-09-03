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
        Schema::table('workflow_states', function (Blueprint $table) {
            $table->string('pic_role')->nullable()->after('label');
            $table->string('pic_contact')->nullable()->after('pic_role');
        });

        Schema::table('histori_status', function (Blueprint $table) {
            $table->text('catatan_kendala')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_states', function (Blueprint $table) {
            $table->dropColumn(['pic_role', 'pic_contact']);
        });

        Schema::table('histori_status', function (Blueprint $table) {
            $table->dropColumn('catatan_kendala');
        });
    }
};
