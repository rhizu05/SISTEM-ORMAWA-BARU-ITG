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
        $peminjaman_tempat = PeminjamanTempat::where('user_id', Auth::id())
            ->with('ruangan')
            ->latest()
            ->get();
        return view('peminjaman.tempat_history', compact('peminjaman_tempat'));
    }

    // Opsi A: Riwayat terpisah - Barang (hanya milik sendiri)
    public function historyBarang()
    {
        $peminjaman_barang = PeminjamanBarang::where('user_id', Auth::id())
            ->latest()
            ->get();
        return view('peminjaman.barang_history', compact('peminjaman_barang'));
    }

    // Menampilkan daftar peminjaman untuk Ormawa (gabungan - legacy, redirect ke tempat)
    public function index()
    {
        $peminjaman_tempat = PeminjamanTempat::where('user_id', Auth::id())
            ->with('ruangan')
            ->latest()
            ->get();
            
        $peminjaman_barang = PeminjamanBarang::where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('peminjaman.index', compact('peminjaman_tempat', 'peminjaman_barang'));
    }

    // Form pinjam ruangan
    public function createTempat()
    {
        $ruangans = MasterRuangan::where('status_aktif', true)->get();
        return view('peminjaman.create_tempat', compact('ruangans'));
    }

    // Store pinjam ruangan
    public function storeTempat(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:master_ruangan,id',
            'nama_kegiatan' => 'required|string|max:255',
            'tgl_mulai' => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'deskripsi_kegiatan' => 'nullable|string'
        ]);

        // Cek konflik jadwal ruangan
        $konflik = PeminjamanTempat::where('ruangan_id', $request->ruangan_id)
            ->where('status_akhir', '!=', 'Ditolak Sarpras')
            ->where('status_akhir', '!=', 'Ditolak BKKH')
            ->where(function($query) use ($request) {
                // Logic cek overlap tanggal & jam sederhana
                $query->whereBetween('tgl_mulai', [$request->tgl_mulai, $request->tgl_selesai])
                      ->orWhereBetween('tgl_selesai', [$request->tgl_mulai, $request->tgl_selesai]);
            })
            ->exists();

        if ($konflik) {
            return back()->withInput()->with('error', 'Ruangan sudah dibooking pada tanggal/waktu tersebut.');
        }

        PeminjamanTempat::create([
            'user_id' => Auth::id(),
            'ruangan_id' => $request->ruangan_id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
        ]);

        return redirect()->route('peminjaman.tempat.index')->with('success', 'Pengajuan peminjaman ruangan berhasil dikirim.');
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
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tgl_mulai' => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'barang_id' => 'required|array|min:1',
            'qty' => 'required|array|min:1',
        ]);

        $kebutuhan = [];
        foreach ($request->barang_id as $key => $id_barang) {
            if (isset($request->qty[$key]) && $request->qty[$key] > 0) {
                $barang = MasterBarang::find($id_barang);
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

        PeminjamanBarang::create([
            'user_id' => Auth::id(),
            'nama_kegiatan' => $request->nama_kegiatan,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'kebutuhan_barang' => $kebutuhan,
        ]);

        return redirect()->route('peminjaman.barang.index')->with('success', 'Pengajuan peminjaman barang berhasil dikirim.');
    }

    // Verifikasi (digunakan oleh BKKH, Sarpras Ruangan, & Sarpras Barang)
    public function antrian()
    {
        $role = Auth::user()->roles->first()->name;
        
        $antrian_tempat = collect();
        $antrian_barang = collect();

        if ($role === 'bkh') {
            $antrian_tempat = PeminjamanTempat::where('status_bkkh', 'pending')->with(['user', 'ruangan'])->latest()->get();
            $antrian_barang = PeminjamanBarang::where('status_bkkh', 'pending')->with('user')->latest()->get();
        } 
        elseif ($role === 'sarpras_ruangan') {
            // Sarpras Ruangan HANYA bisa memproses peminjaman ruangan yang sudah ACC BKKH
            $antrian_tempat = PeminjamanTempat::where('status_bkkh', 'disetujui')->where('status_sarpras', 'pending')->with(['user', 'ruangan'])->latest()->get();
        }
        elseif ($role === 'sarpras_barang') {
            // Sarpras Barang HANYA bisa memproses peminjaman barang yang sudah ACC BKKH
            $antrian_barang = PeminjamanBarang::where('status_bkkh', 'disetujui')->where('status_sarpras', 'pending')->with('user')->latest()->get();
        }

        return view('peminjaman.verifikasi.index', compact('antrian_tempat', 'antrian_barang'));
    }

    public function prosesTempat(Request $request, PeminjamanTempat $peminjaman)
    {
        $role = Auth::user()->roles->first()->name;
        $status = $request->aksi === 'setuju' ? 'disetujui' : 'ditolak';
        
        if ($role === 'bkh') {
            $peminjaman->status_bkkh = $status;
            $peminjaman->status_akhir = $status === 'ditolak' ? 'Ditolak BKKH' : 'Proses Sarpras';
        } elseif ($role === 'sarpras_ruangan') {
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
        
        if ($role === 'bkh') {
            $peminjaman->status_bkkh = $status;
            $peminjaman->status_akhir = $status === 'ditolak' ? 'Ditolak BKKH' : 'Proses Sarpras';
        } elseif ($role === 'sarpras_barang') {
            $peminjaman->status_sarpras = $status;
            $peminjaman->status_akhir = $status === 'ditolak' ? 'Ditolak Sarpras' : 'Selesai / Disetujui';
            
            // Jika disetujui final, kurangi stok master barang
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
}

