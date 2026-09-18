<?php

namespace App\Http\Controllers\Bkhm;

use App\Http\Controllers\Controller;
use App\Mail\TiketUpdateMail;
use App\Models\TiketLayanan;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BkhmTiketController extends Controller
{
    /**
     * Manajemen Konseling Mahasiswa (Rahasia & Tertutup)
     */
    public function konselingIndex()
    {
        $tikets = TiketLayanan::where('kategori', 'konseling')
            ->latest()
            ->paginate(15);

        return view('bkhm.tiket.konseling_index', compact('tikets'));
    }

    public function konselingShow(TiketLayanan $tiket)
    {
        abort_unless($tiket->kategori === 'konseling', 404);
        return view('bkhm.tiket.konseling_show', compact('tiket'));
    }

    public function konselingUpdate(Request $request, TiketLayanan $tiket)
    {
        abort_unless($tiket->kategori === 'konseling', 404);

        $validated = $request->validate([
            'status' => 'required|in:ditinjau_bkhm,jadwal_ditentukan,selesai,ditolak',
            'tanggapan_bkhm' => 'required|string',
            'jadwal_temu' => 'nullable|date',
            'lokasi_atau_link' => 'nullable|string|max:255',
        ]);

        $tiket->update([
            'status' => $validated['status'],
            'tanggapan_bkhm' => $validated['tanggapan_bkhm'],
            'jadwal_temu' => $validated['jadwal_temu'] ?? $tiket->jadwal_temu,
            'lokasi_atau_link' => $validated['lokasi_atau_link'] ?? $tiket->lokasi_atau_link,
        ]);

        // Kirim email notifikasi ke mahasiswa (khusus jadwal atau update umum)
        try {
            if ($validated['status'] === 'jadwal_ditentukan' || !empty($validated['jadwal_temu'])) {
                Mail::to($tiket->email)->send(new \App\Mail\JadwalKonselingMail(
                    $tiket,
                    $validated['tanggapan_bkhm'],
                    $tiket->jadwal_temu ? $tiket->jadwal_temu->format('Y-m-d H:i') : null,
                    $tiket->lokasi_atau_link
                ));
            } else {
                Mail::to($tiket->email)->send(new TiketUpdateMail($tiket, $validated['tanggapan_bkhm']));
            }
        } catch (\Throwable $e) {
        }

        return redirect()->back()->with('success', 'Status dan respons konseling berhasil disimpan serta dikirim ke email mahasiswa.');
    }

    /**
     * Eskalasi Aspirasi yang Diteruskan dari BPM
     */
    public function aspirasiIndex()
    {
        $tikets = TiketLayanan::where('kategori', 'aspirasi')
            ->where(function ($q) {
                $q->whereNotNull('diteruskan_ke_bkhm_at')
                  ->orWhereIn('status', ['diteruskan_ke_bkhm', 'diproses_bkhm', 'ditindaklanjuti', 'selesai', 'ditolak']);
            })
            ->latest('diteruskan_ke_bkhm_at')
            ->paginate(15);

        return view('bkhm.tiket.aspirasi_index', compact('tikets'));
    }

    public function aspirasiUpdate(Request $request, TiketLayanan $tiket)
    {
        abort_unless($tiket->kategori === 'aspirasi', 404);

        $validated = $request->validate([
            'status' => 'required|in:diproses_bkhm,ditindaklanjuti,selesai,ditolak',
            'catatan_bkhm' => 'required|string',
        ]);

        $tiket->update([
            'status' => $validated['status'],
            'catatan_bkhm' => $validated['catatan_bkhm'],
        ]);

        try {
            Mail::to($tiket->email)->send(new TiketUpdateMail($tiket, $validated['catatan_bkhm']));
        } catch (\Throwable $e) {
        }

        return redirect()->back()->with('success', 'Tindak lanjut aspirasi berhasil disimpan.');
    }

    /**
     * Manajemen & Verifikasi Prestasi / Bantuan Delegasi Lomba
     */
    public function prestasiIndex()
    {
        $tikets = TiketLayanan::where('kategori', 'prestasi')
            ->latest()
            ->paginate(15);

        return view('bkhm.tiket.prestasi_index', compact('tikets'));
    }

    public function prestasiUpdate(Request $request, TiketLayanan $tiket)
    {
        abort_unless($tiket->kategori === 'prestasi', 404);

        $validated = $request->validate([
            'status' => 'required|in:diverifikasi_bkhm,disetujui,ditolak',
            'catatan_bkhm' => 'nullable|string',
            'tampil_ke_publik' => 'boolean',
        ]);

        $tiket->update([
            'status' => $validated['status'],
            'catatan_bkhm' => $validated['catatan_bkhm'] ?? null,
            'tampil_ke_publik' => $request->boolean('tampil_ke_publik'),
        ]);

        try {
            $pesan = $validated['catatan_bkhm'] ?? ('Pengajuan Anda telah diverifikasi dengan status: ' . $tiket->status_label);
            Mail::to($tiket->email)->send(new TiketUpdateMail($tiket, $pesan));
        } catch (\Throwable $e) {
        }

        return redirect()->back()->with('success', 'Verifikasi pengajuan prestasi berhasil diperbarui.');
    }
}
