<?php

namespace App\Http\Controllers;

use App\Models\KomunikasiPengajuan;
use App\Models\Pengajuan;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomunikasiController extends Controller
{
    /**
     * FR-011: kirim pesan follow-up pada pengajuan.
     */
    public function store(Request $request, Pengajuan $pengajuan)
    {
        $user = Auth::user();
        $role = $user->roles->first()?->name;

        $isVerifikator = in_array($role, ['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'], true);
        $isOwner = $pengajuan->user_id === $user->id;

        abort_unless($isOwner || $isVerifikator, 403, 'Aksi tidak diizinkan.');

        $request->validate([
            'pesan' => 'required|string|max:2000',
        ]);

        KomunikasiPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $user->id,
            'pesan' => $request->pesan,
        ]);

        // Notifikasi ke pihak lain.
        if ($isOwner) {
            // Beri tahu verifikator yang relevan saat ini.
            $targetRole = match ($pengajuan->state->name) {
                'submitted' => 'bem',
                'bem_approved' => 'bpm',
                'bpm_approved' => 'bkhm',
                'bkhm_approved' => 'wr3',
                'to_treasurer' => 'bendahara',
                default => 'bkhm',
            };
            NotifikasiService::kirimKeRole($targetRole, 'Pesan baru pada pengajuan "' . $pengajuan->nama_kegiatan . '".');
        } else {
            NotifikasiService::kirim($pengajuan->user_id, 'Pesan baru dari verifikator pada pengajuan "' . $pengajuan->nama_kegiatan . '".');
        }

        return redirect()->back()->with('success', 'Pesan follow-up terkirim.');
    }
}
