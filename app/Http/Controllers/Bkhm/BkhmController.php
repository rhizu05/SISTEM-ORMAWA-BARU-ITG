<?php

namespace App\Http\Controllers\Bkhm;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PeminjamanTempat;
use App\Models\PeriodeAnggaran;
use App\Models\SaldoHistori;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BkhmController extends Controller
{
    public function saldo()
    {
        $users = User::role(['ormawa','bem','bpm'])->withCount('pengajuans as total_pengajuan')->get()->map(function($u){
            $terpakai = $u->saldo_awal - $u->saldo;
            if ($terpakai < 0) $terpakai = 0;
            $u->total_terpakai = $terpakai;
            $u->rincian = $u->pengajuans()->latest()->take(3)->pluck('nama_kegiatan')->implode(', ');
            return $u;
        });
        $saldoHistori = SaldoHistori::with(['user', 'actor', 'periode'])->latest()->take(20)->get();

        // Q-BKHM-02: periode anggaran ditetapkan dari rapat pimpinan.
        $periodes = PeriodeAnggaran::orderByDesc('tanggal_mulai')->get();
        $periodeAktif = PeriodeAnggaran::aktif();

        return view('bkhm.saldo', compact('users', 'saldoHistori', 'periodes', 'periodeAktif'));
    }

    /**
     * Q-BKHM-02: buat periode anggaran baru.
     */
    public function storePeriode(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'aktif' => 'boolean',
        ]);

        $periode = PeriodeAnggaran::create([
            'nama' => $validated['nama'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'aktif' => $request->boolean('aktif'),
        ]);

        if ($periode->aktif) {
            PeriodeAnggaran::where('id', '!=', $periode->id)->update(['aktif' => false]);
        }

        return redirect()->route('bkhm.saldo.index')->with('success', 'Periode anggaran berhasil ditambahkan.');
    }

    /**
     * Q-BKHM-02: jadikan satu periode sebagai periode berjalan.
     */
    public function aktifkanPeriode(PeriodeAnggaran $periode)
    {
        PeriodeAnggaran::where('id', '!=', $periode->id)->update(['aktif' => false]);
        $periode->update(['aktif' => true]);

        return redirect()->route('bkhm.saldo.index')->with('success', 'Periode anggaran aktif diperbarui.');
    }

    public function arsipSurat(Request $request)
    {
        $query = Pengajuan::with(['user','state'])->whereNotNull('nomor_surat');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use($s){
                $q->where('nomor_surat','like',"%$s%")->orWhere('nama_kegiatan','like',"%$s%");
            });
        }
        $arsip = $query->latest()->paginate(10)->withQueryString();
        return view('bkhm.arsip', compact('arsip'));
    }

    public function spCreate()
    {
        $ormawas = User::role('ormawa')->get();
        return view('bkhm.sp_create', compact('ormawas'));
    }

    public function spStore(Request $request)
    {
        $request->validate([
            'target_user_id'=>'required|exists:users,id',
            'nomor_surat'=>'required|string|unique:surat_peringatans,nomor_surat',
            'tingkat'=>'required|in:SP-1,SP-2,SP-3',
            'perihal'=>'required|string',
            'alasan_singkat'=>'required|string',
            'deskripsi'=>'required|string',
            'sanksi'=>'required|string',
            'tanggal_surat'=>'required|date',
            'penandatangan'=>'required|string',
        ]);
        $sp = SuratPeringatan::create([
            'target_user_id'=>$request->target_user_id,
            'nomor_surat'=>$request->nomor_surat,
            'tingkat'=>$request->tingkat,
            'perihal'=>$request->perihal,
            'alasan_singkat'=>$request->alasan_singkat,
            'deskripsi'=>$request->deskripsi,
            'sanksi'=>$request->sanksi,
            'tanggal_surat'=>$request->tanggal_surat,
            'penandatangan'=>$request->penandatangan,
            'created_by'=>Auth::id(),
        ]);
        // generate simple PDF placeholder (html printable) - actual pdf via dompdf if needed
        return redirect()->route('bkhm.arsip.index')->with('success','Surat Peringatan berhasil diterbitkan: '.$sp->nomor_surat);
    }

    public function spShow(SuratPeringatan $sp)
    {
        $sp->load(['target','creator']);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        return view('bkhm.sp_show', compact('sp', 'konfig'));
    }

    /**
     * FR-008: unduh Surat Peringatan sebagai PDF (DomPDF).
     */
    public function spPdf(SuratPeringatan $sp)
    {
        $sp->load(['target','creator']);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bkhm.sp_pdf', [
            'sp' => $sp,
            'konfig' => $konfig,
        ])->setPaper('a4');

        return $pdf->download('surat-peringatan-' . \Illuminate\Support\Str::slug($sp->nomor_surat) . '.pdf');
    }

    public function verifikasiTempat()
    {
        $antrian = PeminjamanTempat::with(['user','ruangan'])->where('status_bkhm','pending')->latest()->get();
        return view('bkhm.verifikasi_tempat', compact('antrian'));
    }

    /**
     * Ekspor rekapitulasi data keuangan resmi format Excel (.xlsx).
     */
    public function exportExcel(Request $request)
    {
        return \App\Services\RekapKeuanganExportService::exportExcel($request->periode_id);
    }

    /**
     * Ekspor rekapitulasi data keuangan resmi format PDF.
     */
    public function exportPdf(Request $request)
    {
        return \App\Services\RekapKeuanganExportService::exportPdf($request->periode_id);
    }
}
