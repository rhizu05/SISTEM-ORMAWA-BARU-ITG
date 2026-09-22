<?php

namespace App\Http\Controllers;

use App\Models\KomunikasiPengajuan;
use App\Models\Pengajuan;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NudgeController extends Controller
{
    /**
     * Kirim pengingat cepat (nudge) ke lembaga pemeriksa terkait.
     */
    public function kirim(Request $request, Pengajuan $pengajuan)
    {
        $user = Auth::user();

        // Otorisasi: hanya pemilik pengajuan yang berhak mengirim pengingat
        if ($pengajuan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengirim pengingat pada pengajuan ini.');
        }

        // Cek target lembaga pemeriksa
        $targetRole = $pengajuan->targetRoleNudge();
        if (! $targetRole) {
            return back()->with('error', 'Pengajuan tidak berada dalam tahapan verifikasi aktif.');
        }

        // Cek cooldown 12 jam
        if ($pengajuan->apakahDalamCooldown()) {
            return back()->with(
                'error',
                'Pengingat cepat hanya dapat dikirim setiap 12 jam sekali. Sisa waktu tunggu: ' . $pengajuan->sisaWaktuCooldown() . '.'
            );
        }

        $targetLabel = $pengajuan->targetLembagaLabel();
        $pengajuName = $user->name;

        // 1. Kirim notifikasi in-app ke seluruh akun lembaga pemeriksa
        $pesanNotif = "🔔 [PENGINGAT CEPAT] {$pengajuName} mengirimkan pengingat untuk peninjauan proposal \"{$pengajuan->nama_kegiatan}\".";
        NotifikasiService::kirimKeRole($targetRole, $pesanNotif);

        // 2. Catat jejak audit di thread komunikasi pengajuan
        KomunikasiPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $user->id,
            'pesan' => "🔔 [PENGINGAT CEPAT] Pengusul ({$pengajuName}) telah mengirimkan pengingat verifikasi kepada {$targetLabel} pada " . now()->format('d/m/Y H:i') . " WIB.",
        ]);

        // 3. Perbarui timestamp dan hitungan pengingat
        $pengajuan->update([
            'terakhir_diingatkan_at' => now(),
            'jumlah_nudge' => $pengajuan->jumlah_nudge + 1,
        ]);

        return back()->with(
            'success',
            "Pengingat cepat berhasil dikirimkan kepada {$targetLabel}. Masa tunggu (cooldown) 12 jam telah diaktifkan."
        );
    }
}
