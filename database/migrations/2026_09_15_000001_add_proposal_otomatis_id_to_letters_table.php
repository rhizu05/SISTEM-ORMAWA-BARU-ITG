<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // BASE-04: relasi eksplisit Letter (LPJ/surat) ke ProposalOtomatis
        Schema::table('letters', function (Blueprint $table) {
            if (! Schema::hasColumn('letters', 'proposal_otomatis_id')) {
                $table->foreignId('proposal_otomatis_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('proposal_otomatis')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            if (Schema::hasColumn('letters', 'proposal_otomatis_id')) {
                $table->dropConstrainedForeignId('proposal_otomatis_id');
            }
        });
    }
};
