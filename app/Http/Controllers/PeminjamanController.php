<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\MasterRuangan;
use App\Models\PeminjamanBarang;
use App\Models\PeminjamanTempat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    // Opsi A: Riwayat terpisah - Tempat (hanya milik sendiri)
    public function historyTempat()
    {
        $peminjaman_tempat = (Auth::user()->hasRole('admin') ? PeminjamanTempat::query() : PeminjamanTempat::where('user_id', Auth::id()))
            ->with('ruangan')
            ->latest()
            ->get();
        return view('peminjaman.tempat_history', compact('peminjaman_tempat'));
    }

    // Opsi A: Riwayat terpisah - Barang (hanya milik sendiri)
    public function historyBarang()
    {
        $peminjaman_barang = (Auth::user()->hasRole('admin') ? PeminjamanBarang::query() : PeminjamanBarang::where('user_id', Auth::id()))
            ->latest()
            ->get();
        return view('peminjaman.barang_history', compact('peminjaman_barang'));
    }

    // Menampilkan daftar peminjaman untuk Ormawa (gabungan - legacy, redirect ke tempat)
    public function index()
    {
        $peminjaman_tempat = (Auth::user()->hasRole('admin') ? PeminjamanTempat::query() : PeminjamanTempat::where('user_id', Auth::id()))
            ->with('ruangan')
            ->latest()
            ->get();
            
        $peminjaman_barang = (Auth::user()->hasRole('admin') ? PeminjamanBarang::query() : PeminjamanBarang::where('user_id', Auth::id()))
            ->latest()
            ->get();
            
        return view('peminjaman.index', compact('peminjaman_tempat', 'peminjaman_barang'));
    }

    // Form pinjam ruangan
    public function createTempat()
    {
        $ruangans = MasterRuangan::where('status_aktif', true)->get();

        // FR-018 / UI-013: kalender ketersediaan ruangan (gabungan peminjaman aktif + jadwal perkuliahan)
        $peminjamanTempat = PeminjamanTempat::with('ruangan')
            ->whereIn('status_akhir', ['Selesai / Disetujui', 'Proses Sarpras'])
            ->where('tgl_selesai', '>=', now()->toDateString())
            ->get();

        // Generate visual events 30 hari ke depan untuk jadwal perkuliahan rutin
        $jadwalKuliahAktif = \App\Models\JadwalKuliah::with('ruangan')
            ->where('aktif', true)
            ->get();

        $kuliahEvents = [];
        $startDate = now()->startOfWeek();
        $endDate = now()->addDays(30);

        for ($curr = $startDate->copy(); $curr->lte($endDate); $curr->addDay()) {
            $isoDay = $curr->isoWeekday();
            $dateStr = $curr->toDateString();

            foreach ($jadwalKuliahAktif->where('hari', $isoDay) as $jk) {
                $kuliahEvents[] = [
                    'title' => '[Kuliah] ' . $jk->mata_kuliah . ' (' . ($jk->ruangan->nama_ruangan ?? '-') . ')',
                    'start' => $dateStr . 'T' . substr($jk->jam_mulai, 0, 5),
                    'end' => $dateStr . 'T' . substr($jk->jam_selesai, 0, 5),
                    'color' => '#2563eb', // Biru
                    'ruangan_id' => $jk->ruangan_id,
                ];
            }
        }

        return view('peminjaman.create_tempat', compact('ruangans', 'peminjamanTempat', 'kuliahEvents'));
    }

    /**
     * Endpoint ketersediaan jadwal ruangan untuk tanggal tertentu (AJAX Slot Checker)
     */
    public function jadwalRuangan(Request $request)
    {
        $ruanganId = $request->query('ruangan_id');
        $tanggal = $request->query('tanggal', now()->toDateString());

        if (! $ruanganId) {
            return response()->json(['kuliah' => [], 'peminjaman' => []]);
        }

        $date = \Carbon\Carbon::parse($tanggal);
        $dayOfWeek = $date->isoWeekday();

        $kuliah = \App\Models\JadwalKuliah::where('ruangan_id', $ruanganId)
            ->where('hari', $dayOfWeek)
            ->where('aktif', true)
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn($k) => [
                'tipe' => 'kuliah',
                'judul' => $k->mata_kuliah . ($k->semester ? ' (Sem ' . $k->semester . ')' : ''),
                'jam_mulai' => substr($k->jam_mulai, 0, 5),
                'jam_selesai' => substr($k->jam_selesai, 0, 5),
            ]);

        $peminjaman = PeminjamanTempat::where('ruangan_id', $ruanganId)
            ->whereNotIn('status_akhir', ['Ditolak Sarpras', 'Ditolak BKHM'])
            ->where('tgl_mulai', '<=', $tanggal)
            ->where('tgl_selesai', '>=', $tanggal)
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn($p) => [
                'tipe' => 'peminjaman',
                'judul' => $p->nama_kegiatan . ' (' . $p->status_akhir . ')',
                'jam_mulai' => substr($p->jam_mulai, 0, 5),
                'jam_selesai' => substr($p->jam_selesai, 0, 5),
            ]);

        return response()->json([
            'ruangan_id' => $ruanganId,
            'tanggal' => $tanggal,
            'hari_nama' => \App\Models\JadwalKuliah::HARI[$dayOfWeek] ?? '',
            'kuliah' => $kuliah,
            'peminjaman' => $peminjaman,
        ]);
    }

    // Store pinjam ruangan
    public function storeTempat(Request $request)
    {
        $isHima = Auth::user()->isHima();

        $rules = [
            'ruangan_id' => 'required|exists:master_ruangan,id',
            'nama_kegiatan' => 'required|string|max:255',
            'tgl_mulai' => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'deskripsi_kegiatan' => 'nullable|string',
        ];

        // Q-SAR-04: HIMA wajib melampirkan dokumen persetujuan Prodi.
        if ($isHima) {
            $rules['file_persetujuan_prodi'] = 'required|file|mimes:pdf|mimetypes:application/pdf|max:5120';
        } else {
            $rules['file_persetujuan_prodi'] = 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:5120';
        }

        $request->validate($rules);

        // Cek konflik jadwal ruangan (overlap tanggal + jam secara presisi)
        $newStart = \Carbon\Carbon::parse($request->tgl_mulai.' '.$request->jam_mulai);
        $newEnd = \Carbon\Carbon::parse($request->tgl_selesai.' '.$request->jam_selesai);

        $konflik = PeminjamanTempat::where('ruangan_id', $request->ruangan_id)
            ->whereNotIn('status_akhir', ['Ditolak Sarpras', 'Ditolak BKHM'])
            // Kandidat: rentang tanggal beririsan (filter kasar di DB)
            ->where('tgl_mulai', '<=', $request->tgl_selesai)
            ->where('tgl_selesai', '>=', $request->tgl_mulai)
            ->get()
            // Presisi: existing.start < new.end AND existing.end > new.start
            ->contains(function ($existing) use ($newStart, $newEnd) {
                $existingStart = \Carbon\Carbon::parse($existing->tgl_mulai.' '.$existing->jam_mulai);
                $existingEnd = \Carbon\Carbon::parse($existing->tgl_selesai.' '.$existing->jam_selesai);

                return $existingStart->lt($newEnd) && $existingEnd->gt($newStart);
            });

        if ($konflik) {
            return back()->withInput()->with('error', 'Ruangan sudah dibooking pada tanggal/waktu tersebut.');
        }

        // Q-SAR-02: cek bentrok dengan jadwal perkuliahan (pola mingguan).
        if (\App\Models\JadwalKuliah::bentrok(
            (int) $request->ruangan_id,
            $request->tgl_mulai,
            $request->tgl_selesai,
            $request->jam_mulai,
            $request->jam_selesai,
        )) {
            return back()->withInput()->with('error', 'Waktu yang dipilih bentrok dengan jadwal perkuliahan di ruangan tersebut.');
        }

        $dokumenProdi = null;
        if ($request->hasFile('file_persetujuan_prodi')) {
            $dokumenProdi = $request->file('file_persetujuan_prodi')
                ->storeAs('persetujuan-prodi', time() . '_prodi.pdf', 'local');
        }

        $isDirectToSarpras = (Auth::user()->hasRole('ormawa') || $isHima) && $dokumenProdi !== null;

        PeminjamanTempat::create([
            'user_id' => Auth::id(),
            'ruangan_id' => $request->ruangan_id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'file_persetujuan_prodi' => $dokumenProdi,
            'status_bkhm' => $isDirectToSarpras ? 'disetujui' : 'pending',
            'status_sarpras' => 'pending',
            'status_akhir' => $isDirectToSarpras ? 'Proses Sarpras' : 'Proses BKHM',
        ]);

        if ($isDirectToSarpras) {
            \App\Services\NotifikasiService::kirimKeRole('sarpras', 'Peminjaman ruangan baru dari Ormawa: "' . $request->nama_kegiatan . '" (Surat Prodi terlampir, langsung ke Sarpras).');
            return redirect()->route('peminjaman.tempat.index')->with('success', 'Pengajuan peminjaman ruangan berhasil dikirim langsung ke Sarpras (Surat Prodi terlampir).');
        }

        \App\Services\NotifikasiService::kirimKeRole('bkhm', 'Peminjaman ruangan baru diajukan: "' . $request->nama_kegiatan . '".');
        return redirect()->route('peminjaman.tempat.index')->with('success', 'Pengajuan peminjaman ruangan berhasil dikirim ke BKHM.');
    }

    // Form pinjam barang
    public function createBarang()
    {
        $barangs = MasterBarang::where('status_aktif', true)->where('stok_tersedia', '>', 0)->get();
        return view('peminjaman.create_barang', compact('barangs'));
    }

    // Store pinjam barang
    public function storeBarang(Request $request)
    {
        $isHima = Auth::user()->isHima();

        $rules = [
            'nama_kegiatan' => 'required|string|max:255',
            'tgl_mulai' => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'barang_id' => 'required|array|min:1',
            'qty' => 'required|array|min:1',
        ];

        // Q-SAR-04: HIMA wajib melampirkan dokumen persetujuan Prodi.
        $rules['file_persetujuan_prodi'] = ($isHima ? 'required' : 'nullable')
            . '|file|mimes:pdf|mimetypes:application/pdf|max:5120';

        $request->validate($rules);

        $kebutuhan = [];
        foreach ($request->barang_id as $key => $id_barang) {
            if (isset($request->qty[$key]) && $request->qty[$key] > 0) {
                $barang = MasterBarang::find($id_barang);
                if ($barang && ! $barang->boleh_dibawa_keluar) {
                    return back()->withInput()->with('error', 'Barang ' . $barang->nama_barang . ' tidak boleh dibawa keluar kampus.');
                }
                if ($barang && $barang->stok_tersedia >= $request->qty[$key]) {
                    $kebutuhan[] = [
                        'id_barang' => $id_barang,
                        'nama_barang' => $barang->nama_barang,
                        'qty' => $request->qty[$key]
                    ];
                } else {
                    return back()->withInput()->with('error', 'Stok untuk barang ' . ($barang->nama_barang ?? 'tidak diketahui') . ' tidak mencukupi.');
                }
            }
        }

        if (empty($kebutuhan)) {
            return back()->withInput()->with('error', 'Harap pilih minimal 1 barang dengan quantity > 0.');
        }

        $dokumenProdi = null;
        if ($request->hasFile('file_persetujuan_prodi')) {
            $dokumenProdi = $request->file('file_persetujuan_prodi')
                ->storeAs('persetujuan-prodi', time() . '_prodi.pdf', 'local');
        }

        $isDirectToSarpras = (Auth::user()->hasRole('ormawa') || $isHima) && $dokumenProdi !== null;

        PeminjamanBarang::create([
            'user_id' => Auth::id(),
            'nama_kegiatan' => $request->nama_kegiatan,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'kebutuhan_barang' => $kebutuhan,
            'file_persetujuan_prodi' => $dokumenProdi,
            'status_bkhm' => $isDirectToSarpras ? 'disetujui' : 'pending',
            'status_sarpras' => 'pending',
            'status_akhir' => $isDirectToSarpras ? 'Proses Sarpras' : 'Proses BKHM',
        ]);

        if ($isDirectToSarpras) {
            \App\Services\NotifikasiService::kirimKeRole('sarpras', 'Peminjaman barang baru dari Ormawa: "' . $request->nama_kegiatan . '" (Surat Prodi terlampir, langsung ke Sarpras).');
            return redirect()->route('peminjaman.barang.index')->with('success', 'Pengajuan peminjaman barang berhasil dikirim langsung ke Sarpras (Surat Prodi terlampir).');
        }

        \App\Services\NotifikasiService::kirimKeRole('bkhm', 'Peminjaman barang baru diajukan: "' . $request->nama_kegiatan . '".');
        return redirect()->route('peminjaman.barang.index')->with('success', 'Pengajuan peminjaman barang berhasil dikirim ke BKHM.');
    }

    // Verifikasi (digunakan oleh BKHM, Sarpras Ruangan, & Sarpras Barang)
    public function antrian()
    {
        $role = Auth::user()->roles->first()->name;
        
        $antrian_tempat = collect();
        $antrian_barang = collect();
        $barangDipinjam = collect();

        if ($role === 'admin') {
            $antrian_tempat = PeminjamanTempat::with(['user', 'ruangan'])->latest()->get();
            $antrian_barang = PeminjamanBarang::with('user')->latest()->get();
            $barangDipinjam = PeminjamanBarang::with('user')->where('status_akhir', 'Sedang Digunakan')->latest()->get();
        } elseif ($role === 'bkhm') {
            $antrian_tempat = PeminjamanTempat::where('status_bkhm', 'pending')->with(['user', 'ruangan'])->latest()->get();
            $antrian_barang = PeminjamanBarang::where('status_bkhm', 'pending')->with('user')->latest()->get();
        }
        elseif ($role === 'sarpras') {
            // Q-SAR-01: role Sarpras disatukan (ruangan + barang).
            // Hanya memproses peminjaman yang sudah ACC BKHM.
            $antrian_tempat = PeminjamanTempat::where('status_bkhm', 'disetujui')->where('status_sarpras', 'pending')->with(['user', 'ruangan'])->latest()->get();
            $antrian_barang = PeminjamanBarang::where('status_bkhm', 'disetujui')->where('status_sarpras', 'pending')->with('user')->latest()->get();
            // BASE-06: barang yang sedang dipinjam & perlu validasi kembali
            $barangDipinjam = PeminjamanBarang::with('user')->where('status_akhir', 'Sedang Digunakan')->latest()->get();
        }

        return view('peminjaman.verifikasi.index', compact('antrian_tempat', 'antrian_barang', 'barangDipinjam'));
    }

    public function prosesTempat(Request $request, PeminjamanTempat $peminjaman)
    {
        $role = Auth::user()->roles->first()->name;
        $status = $request->aksi === 'setuju' ? 'disetujui' : 'ditolak';
        
        if ($role === 'bkhm') {
            $peminjaman->status_bkhm = $status;
            $peminjaman->status_akhir = $status === 'ditolak' ? 'Ditolak BKHM' : 'Proses Sarpras';
        } elseif ($role === 'sarpras') {
            $peminjaman->status_sarpras = $status;
            $peminjaman->status_akhir = $status === 'ditolak' ? 'Ditolak Sarpras' : 'Selesai / Disetujui';
        }

        if ($status === 'ditolak') {
            $peminjaman->catatan_penolakan = $request->catatan;
        }

        $peminjaman->save();
        return redirect()->back()->with('success', 'Verifikasi peminjaman tempat berhasil disimpan.');
    }

    public function prosesBarang(Request $request, PeminjamanBarang $peminjaman)
    {
        $role = Auth::user()->roles->first()->name;
        $status = $request->aksi === 'setuju' ? 'disetujui' : 'ditolak';
        
        if ($role === 'bkhm') {
            $peminjaman->status_bkhm = $status;
            $peminjaman->status_akhir = $status === 'ditolak' ? 'Ditolak BKHM' : 'Proses Sarpras';
        } elseif ($role === 'sarpras') {
            $peminjaman->status_sarpras = $status;
            $peminjaman->status_akhir = $status === 'ditolak' ? 'Ditolak Sarpras' : 'Sedang Digunakan';

            // BASE-06: stok berkurang saat barang divalidasi keluar.
            if ($status === 'disetujui') {
                foreach ($peminjaman->kebutuhan_barang as $item) {
                    $barang = MasterBarang::find($item['id_barang']);
                    if ($barang) {
                        $barang->decrement('stok_tersedia', $item['qty']);
                    }
                }
            }
        }

        if ($status === 'ditolak') {
            $peminjaman->catatan_penolakan = $request->catatan;
        }

        $peminjaman->save();
        return redirect()->back()->with('success', 'Verifikasi peminjaman barang berhasil disimpan.');
    }

    // BASE-06: validasi barang kembali & pemulihan stok
    public function kembalikanBarang(PeminjamanBarang $peminjaman)
    {
        $role = Auth::user()->roles->first()->name;

        if (! in_array($role, ['sarpras', 'admin'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($peminjaman->status_akhir !== 'Sedang Digunakan') {
            return redirect()->back()->with('error', 'Barang belum dalam status dipinjam atau sudah dikembalikan.');
        }

        foreach ($peminjaman->kebutuhan_barang as $item) {
            $barang = MasterBarang::find($item['id_barang']);
            if ($barang) {
                $barang->increment('stok_tersedia', $item['qty']);
            }
        }

        $peminjaman->status_akhir = 'Dikembalikan';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Barang berhasil divalidasi kembali dan stok diperbarui.');
    }
}

