<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-012: LPJ kini diverifikasi BKHM lalu WR3 (lpj_wr3_review) sebelum selesai.
        // Hapus transisi lama lpj_submitted -> completed (oleh BKHM) bila masih ada.
        if (! Schema::hasTable('workflow_states') || ! Schema::hasTable('workflow_transitions')) {
            return;
        }

        $lpjSubmitted = DB::table('workflow_states')->where('name', 'lpj_submitted')->value('id');
        $completed = DB::table('workflow_states')->where('name', 'completed')->value('id');

        if ($lpjSubmitted && $completed) {
            DB::table('workflow_transitions')
                ->where('from_state_id', $lpjSubmitted)
                ->where('to_state_id', $completed)
                ->where('required_role', 'bkhm')
                ->delete();
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan: transisi lama tidak sesuai alur final.
    }
};
