<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penyesuaian alur BKHM muncul dua kali: perjelas label state pada DB existing.
        if (! Schema::hasTable('workflow_states')) {
            return;
        }

        $labels = [
            'bpm_approved' => ['label' => 'Verifikasi BKHM (Tahap 1)', 'pic_role' => 'BKHM'],
            'wr3_approved' => ['label' => 'Disetujui WR3 - Siap Diajukan ke Bendahara', 'pic_role' => 'BKHM (Tahap 2) / Bendahara'],
        ];

        foreach ($labels as $name => $data) {
            DB::table('workflow_states')->where('name', $name)->update($data);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('workflow_states')) {
            return;
        }

        DB::table('workflow_states')->where('name', 'bpm_approved')->update([
            'label' => 'Verifikasi BKHM',
            'pic_role' => 'BKHM',
        ]);

        DB::table('workflow_states')->where('name', 'wr3_approved')->update([
            'label' => 'Disetujui WR3',
            'pic_role' => 'BKHM / Bendahara',
        ]);
    }
};
