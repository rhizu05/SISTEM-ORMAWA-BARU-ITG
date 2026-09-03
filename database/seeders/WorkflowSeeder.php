<?php

namespace Database\Seeders;

use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => 'draft', 'label' => 'Draft', 'order_num' => 1, 'pic_role' => 'Ormawa Pengaju', 'pic_contact' => 'Ketua Ormawa / Seskab'],
            ['name' => 'submitted', 'label' => 'Diajukan ke BEM', 'order_num' => 2, 'pic_role' => 'Pengurus BEM', 'pic_contact' => 'Divisi Administrasi BEM'],
            ['name' => 'bem_approved', 'label' => 'Verifikasi BPM', 'order_num' => 3, 'pic_role' => 'Pengurus BPM', 'pic_contact' => 'Komisi Pengawasan BPM'],
            ['name' => 'bpm_approved', 'label' => 'Verifikasi BKKH', 'order_num' => 4, 'pic_role' => 'BKKH', 'pic_contact' => 'Bapak Encep (BKKH)'],
            ['name' => 'bkh_approved', 'label' => 'Verifikasi WR3', 'order_num' => 5, 'pic_role' => 'Wakil Rektor III', 'pic_contact' => 'Kantor WR III'],
            ['name' => 'wr3_approved', 'label' => 'Disetujui WR3', 'order_num' => 6, 'pic_role' => 'BKKH / Bendahara', 'pic_contact' => 'Bagian Keuangan'],
            ['name' => 'to_treasurer', 'label' => 'Diajukan ke Bendahara', 'order_num' => 7, 'pic_role' => 'Bendahara Kampus', 'pic_contact' => 'Bagian Keuangan/Bendahara'],
            ['name' => 'funds_disbursed', 'label' => 'Dana Cair', 'order_num' => 8, 'pic_role' => 'Ormawa Pengaju', 'pic_contact' => 'Pelaksana Kegiatan'],
            ['name' => 'lpj_submitted', 'label' => 'LPJ Diajukan', 'order_num' => 9, 'pic_role' => 'BKKH', 'pic_contact' => 'Bapak Encep (BKKH)'],
            ['name' => 'completed', 'label' => 'Selesai', 'order_num' => 10, 'pic_role' => 'Sistem / Arsip', 'pic_contact' => 'Arsip Digital SKIN'],
            ['name' => 'rejected', 'label' => 'Ditolak', 'order_num' => 99, 'pic_role' => 'Ormawa Pengaju', 'pic_contact' => 'Lihat Catatan Kendala'],
        ];

        foreach ($states as $state) {
            WorkflowState::firstOrCreate(['name' => $state['name']], $state);
        }

        // Setup transitions
        $transitions = [
            ['from' => 'draft', 'to' => 'submitted', 'label' => 'Ajukan', 'role' => 'ormawa'],
            
            // BEM
            ['from' => 'submitted', 'to' => 'bem_approved', 'label' => 'Setujui', 'role' => 'bem'],
            ['from' => 'submitted', 'to' => 'rejected', 'label' => 'Tolak', 'role' => 'bem'],
            ['from' => 'submitted', 'to' => 'draft', 'label' => 'Revisi', 'role' => 'bem'],
            
            // BPM
            ['from' => 'bem_approved', 'to' => 'bpm_approved', 'label' => 'Setujui', 'role' => 'bpm'],
            ['from' => 'bem_approved', 'to' => 'rejected', 'label' => 'Tolak', 'role' => 'bpm'],
            ['from' => 'bem_approved', 'to' => 'draft', 'label' => 'Revisi', 'role' => 'bpm'],
            
            // BKH
            ['from' => 'bpm_approved', 'to' => 'bkh_approved', 'label' => 'Setujui', 'role' => 'bkh'],
            ['from' => 'bpm_approved', 'to' => 'rejected', 'label' => 'Tolak', 'role' => 'bkh'],
            ['from' => 'bpm_approved', 'to' => 'draft', 'label' => 'Revisi', 'role' => 'bkh'],
            
            // WR3
            ['from' => 'bkh_approved', 'to' => 'wr3_approved', 'label' => 'Setujui', 'role' => 'wr3'],
            ['from' => 'bkh_approved', 'to' => 'rejected', 'label' => 'Tolak', 'role' => 'wr3'],
            ['from' => 'bkh_approved', 'to' => 'draft', 'label' => 'Revisi', 'role' => 'wr3'],
            
            // BKH to Treasurer
            ['from' => 'wr3_approved', 'to' => 'to_treasurer', 'label' => 'Ajukan Pencairan', 'role' => 'bkh'],
            
            // Treasurer
            ['from' => 'to_treasurer', 'to' => 'funds_disbursed', 'label' => 'Cairkan', 'role' => 'bendahara'],
            
            // Ormawa
            ['from' => 'funds_disbursed', 'to' => 'lpj_submitted', 'label' => 'Kirim LPJ', 'role' => 'ormawa'],
            
            // BKH (LPJ)
            ['from' => 'lpj_submitted', 'to' => 'completed', 'label' => 'Setujui LPJ', 'role' => 'bkh'],
            ['from' => 'lpj_submitted', 'to' => 'funds_disbursed', 'label' => 'Revisi LPJ', 'role' => 'bkh'],
        ];

        foreach ($transitions as $transition) {
            $from = WorkflowState::where('name', $transition['from'])->first();
            $to = WorkflowState::where('name', $transition['to'])->first();
            
            if ($from && $to) {
                WorkflowTransition::firstOrCreate([
                    'from_state_id' => $from->id,
                    'to_state_id' => $to->id,
                    'action_label' => $transition['label'],
                    'required_role' => $transition['role']
                ]);
            }
        }
    }
}
