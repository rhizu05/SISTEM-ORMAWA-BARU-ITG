<?php

namespace App\Http\Controllers\Bkhm;

use App\Http\Controllers\Controller;
use App\Models\Konfigurasi;
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
        $arsip = $query->latest()->paginate(10, ['*'], 'pengajuan_page')->withQueryString();

        $arsipSp = SuratPeringatan::with(['target','creator'])->latest()->paginate(10, ['*'], 'sp_page');

        $skQuery = User::role(['ormawa', 'bem', 'bpm'])->whereNotNull('file_sk')->with('roles');
        if ($request->filled('search_sk')) {
            $s = $request->search_sk;
            $skQuery->where(function($q) use($s){
                $q->where('name', 'like', "%$s%")
                  ->orWhere('nomor_sk', 'like', "%$s%")
                  ->orWhere('username', 'like', "%$s%");
            });
        }
        $arsipSk = $skQuery->latest()->paginate(10, ['*'], 'sk_page')->withQueryString();

        return view('bkhm.arsip', compact('arsip', 'arsipSp', 'arsipSk'));
    }

    public function spCreate()
    {
        $ormawas = User::role(['ormawa', 'bem', 'bpm'])->orderBy('name')->get();
        $prodis = [
            'S1 Teknik Informatika',
            'S1 Teknik Sipil',
            'S1 Teknik Industri',
            'S1 Sistem Informasi',
            'S1 Arsitektur',
        ];

        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        $wr3Nama = ($konfig['wr3_nama'] ?? null) ?: 'Pejabat Wakil Rektor III';
        $wr3Nidn = ($konfig['wr3_nidn'] ?? null) ?: '-';
        $wr3Jabatan = $konfig['wr3_jabatan'] ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama';

        $wr3Info = [
            'nama' => $wr3Nama,
            'nidn' => $wr3Nidn,
            'jabatan' => $wr3Jabatan,
        ];

        return view('bkhm.sp_create', compact('ormawas', 'prodis', 'wr3Info'));
    }

    public function spStore(Request $request)
    {
        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        $defaultWr3Nama = ($konfig['wr3_nama'] ?? null) ?: 'Pejabat Wakil Rektor III';
        $defaultWr3Nidn = ($konfig['wr3_nidn'] ?? null) ?: '-';
        $defaultWr3Jabatan = $konfig['wr3_jabatan'] ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama';

        $request->merge([
            'tipe_sasaran' => $request->input('tipe_sasaran', 'ormawa'),
            'pejabat_nama' => $defaultWr3Nama,
            'pejabat_jabatan' => $defaultWr3Jabatan,
            'pejabat_nidn' => $defaultWr3Nidn,
        ]);

        $tipe = $request->input('tipe_sasaran', 'ormawa');

        $rules = [
            'tipe_sasaran' => 'required|in:ormawa,mahasiswa',
            'nomor_surat' => 'required|string|unique:surat_peringatans,nomor_surat',
            'tingkat' => 'required|in:SP-1,SP-2,SP-3',
            'perihal' => 'required|string|max:255',
            'alasan_singkat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'sanksi' => 'required|string',
            'tanggal_surat' => 'required|date',
        ];

        if ($tipe === 'mahasiswa') {
            $rules['target_mahasiswas'] = 'required|array|min:1';
            $rules['target_mahasiswas.*.nim'] = 'required|string|max:50';
            $rules['target_mahasiswas.*.nama'] = 'required|string|max:255';
            $rules['target_mahasiswas.*.prodi'] = 'required|string|max:255';
            $rules['target_mahasiswas.*.kontak'] = 'nullable|string|max:255';
        } else {
            $rules['target_user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        // Daftar penerima mahasiswa (bisa lebih dari satu) disimpan sebagai JSON.
        $mahasiswas = [];
        if ($tipe === 'mahasiswa') {
            foreach ($request->input('target_mahasiswas', []) as $m) {
                $mahasiswas[] = [
                    'nim' => $m['nim'] ?? null,
                    'nama' => $m['nama'] ?? null,
                    'prodi' => $m['prodi'] ?? null,
                    'kontak' => $m['kontak'] ?? null,
                ];
            }
        }
        $pertama = $mahasiswas[0] ?? null;

        $sp = SuratPeringatan::create([
            'tipe_sasaran' => $tipe,
            'target_user_id' => $tipe === 'mahasiswa' ? null : $request->target_user_id,
            'target_mahasiswas' => $tipe === 'mahasiswa' ? $mahasiswas : null,
            'target_nim' => $pertama['nim'] ?? null,
            'target_nama' => $pertama['nama'] ?? null,
            'target_prodi' => $pertama['prodi'] ?? null,
            'target_kontak' => $pertama['kontak'] ?? null,
            'nomor_surat' => $request->nomor_surat,
            'tingkat' => $request->tingkat,
            'status' => 'menunggu_validasi',
            'perihal' => $request->perihal,
            'alasan_singkat' => $request->alasan_singkat,
            'deskripsi' => $request->deskripsi,
            'sanksi' => $request->sanksi,
            'tanggal_surat' => $request->tanggal_surat,
            'penandatangan' => $request->pejabat_nama,
            'pejabat_nama' => $request->pejabat_nama,
            'pejabat_nidn' => $request->pejabat_nidn,
            'pejabat_jabatan' => $request->pejabat_jabatan,
            'created_by' => Auth::id(),
        ]);

        // Beri tahu Wakil Rektor III bahwa ada draf SP baru yang membutuhkan validasi
        \App\Services\NotifikasiService::kirimKeRole(
            'wr3',
            'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') untuk ' . $sp->nama_penerima . ' telah dibuat oleh BKHM dan menunggu validasi Anda.'
        );

        return redirect()->route('bkhm.arsip.index')->with('success', 'Draf Surat Peringatan berhasil dibuat dan diajukan ke Wakil Rektor III untuk divalidasi: ' . $sp->nomor_surat);
    }

    public function spShow(SuratPeringatan $sp)
    {
        $sp->load(['target','creator','validator']);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $signature = null;
        $qrCodeDataUri = null;

        if ($sp->isDisetujui()) {
            $signature = \App\Models\TandaTanganDigital::where('signable_type', get_class($sp))->where('signable_id', $sp->id)->latest()->first();
            if ($signature) {
                $qrCodeDataUri = \App\Services\DigitalSignatureService::generateQrCodeDataUri($signature->verification_url, 120);
            }
        }

        return view('bkhm.sp_show', compact('sp', 'konfig', 'signature', 'qrCodeDataUri'));
    }

    /**
     * FR-008: unduh Surat Peringatan sebagai PDF (DomPDF).
     */
    public function spPdf(SuratPeringatan $sp)
    {
        $sp->load(['target','creator','validator']);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        $signature = null;
        $qrCodeDataUri = null;

        if ($sp->isDisetujui()) {
            $signature = \App\Models\TandaTanganDigital::where('signable_type', get_class($sp))->where('signable_id', $sp->id)->latest()->first();
            if ($signature) {
                $qrCodeDataUri = \App\Services\DigitalSignatureService::generateQrCodeDataUri($signature->verification_url, 120);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bkhm.sp_pdf', [
            'sp'            => $sp,
            'konfig'        => $konfig,
            'signature'     => $signature,
            'qrCodeDataUri' => $qrCodeDataUri,
        ])->setPaper('a4');

        return $pdf->download('surat-peringatan-' . \Illuminate\Support\Str::slug($sp->nomor_surat) . '.pdf');
    }

    /**
     * Tinjauan BKHM atas draf SP dari BPM: teruskan ke WR3.
     */
    public function spTeruskan(Request $request, SuratPeringatan $sp)
    {
        if (! $sp->isMenungguBkhm()) {
            return redirect()->route('bkhm.arsip.index')->with('error', 'Dokumen ini tidak dalam tahap tinjauan BKHM.');
        }

        $request->validate([
            'catatan_bkhm' => 'nullable|string|max:500',
        ]);

        $sp->update([
            'status' => 'menunggu_validasi',
            'catatan_bkhm' => $request->input('catatan_bkhm'),
        ]);

        \App\Services\NotifikasiService::kirimKeRole(
            'wr3',
            'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') dari BPM untuk ' . $sp->nama_penerima . ' telah diteruskan BKHM dan menunggu validasi Anda.'
        );

        if ($sp->created_by) {
            \App\Services\NotifikasiService::kirim(
                $sp->created_by,
                'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') telah ditinjau dan diteruskan oleh BKHM ke Wakil Rektor III.'
            );
        }

        return redirect()->route('bkhm.arsip.index')->with('success', 'Draf SP ' . $sp->nomor_surat . ' diteruskan ke Wakil Rektor III.');
    }

    /**
     * Tinjauan BKHM atas draf SP dari BPM: kembalikan ke BPM dengan catatan.
     */
    public function spKembalikan(Request $request, SuratPeringatan $sp)
    {
        if (! $sp->isMenungguBkhm()) {
            return redirect()->route('bkhm.arsip.index')->with('error', 'Dokumen ini tidak dalam tahap tinjauan BKHM.');
        }

        $request->validate([
            'catatan_bkhm' => 'required|string|min:5|max:1000',
        ], [
            'catatan_bkhm.required' => 'Wajib memberikan catatan alasan pengembalian agar BPM dapat memperbaiki draf SP.',
            'catatan_bkhm.min' => 'Catatan pengembalian minimal 5 karakter.',
        ]);

        $sp->update([
            'status' => 'ditolak',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'catatan_bkhm' => $request->catatan_bkhm,
        ]);

        if ($sp->created_by) {
            \App\Services\NotifikasiService::kirim(
                $sp->created_by,
                'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') dikembalikan oleh BKHM dengan catatan: ' . $sp->catatan_bkhm
            );
        }

        \App\Services\NotifikasiService::kirimKeRole(
            'bpm',
            'Draf Surat Peringatan ' . $sp->tingkat . ' (' . $sp->nomor_surat . ') dikembalikan oleh BKHM untuk revisi.'
        );

        return redirect()->route('bkhm.arsip.index')->with('info', 'Draf SP ' . $sp->nomor_surat . ' dikembalikan ke BPM dengan catatan revisi.');
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
