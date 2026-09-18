<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Regulasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        $kategoriFilter = $request->query('kategori');

        $query = Pengumuman::published()->with(['user', 'disetujuiOleh'])->latest();

        if ($kategoriFilter && $kategoriFilter !== 'semua') {
            $query->where('kategori', $kategoriFilter);
        }

        $pengumuman = $query->get();
        $regulasi = Regulasi::with('user')->latest()->get();

        $pengumumanSaya = collect();
        $antreanKurasiCount = 0;

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('ormawa')) {
                $pengumumanSaya = Pengumuman::where('user_id', $user->id)->latest()->get();
            }
            if ($user->hasRole('bem') || $user->hasRole('admin')) {
                $antreanKurasiCount = Pengumuman::pendingKurasi()->count();
            }
        }
        
        return view('informasi.index', compact('pengumuman', 'regulasi', 'pengumumanSaya', 'antreanKurasiCount', 'kategoriFilter'));
    }

    public function storePengumuman(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasAnyRole(['bem', 'bkhm', 'ormawa', 'admin']), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'nullable|string|max:50',
            'tanggal_kegiatan' => 'nullable|date',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('file_lampiran')) {
            // SEC-01: file disimpan di disk privat, disajikan lewat controller.
            $lampiranPath = $request->file('file_lampiran')->store('pengumuman', 'local');
        }

        // Tentukan status & kategori sesuai role pengunggah (Saran A)
        if ($user->hasRole('bkhm') || $user->hasRole('admin')) {
            $status = 'published';
            $kategori = $request->kategori ?: 'resmi_kampus';
            $flashMsg = 'Pengumuman resmi kampus berhasil diterbitkan.';
        } elseif ($user->hasRole('bem')) {
            $status = 'published';
            $kategori = $request->kategori ?: 'kegiatan_kemahasiswaan';
            $flashMsg = 'Pengumuman BEM berhasil diterbitkan.';
        } else {
            // HIMA & UKM (ormawa): masuk antrean kurasi BEM
            $status = 'pending_kurasi';
            $kategori = 'kegiatan_kemahasiswaan';
            $flashMsg = 'Pengajuan berita berhasil dikirim dan menunggu kurasi BEM sebelum diterbitkan.';
        }

        Pengumuman::create([
            'user_id' => $user->id,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'kategori' => $kategori,
            'status' => $status,
            'tanggal_kegiatan' => $request->tanggal_kegiatan,
            'file_lampiran' => $lampiranPath,
        ]);

        // Notifikasi:
        if ($status === 'published') {
            // FR-022: pengumuman resmi broadcast ke semua pengguna
            \App\Services\NotifikasiService::kirimKeSemua('Pengumuman baru: "' . $request->judul . '".');
        } else {
            // Pengajuan HIMA/UKM hanya memberitahu pengurus BEM (tanpa spam ke semua user)
            \App\Services\NotifikasiService::kirimKeRole('bem', 'Pengajuan berita baru dari ' . $user->name . ': "' . $request->judul . '". Silakan periksa di antrean kurasi BEM.');
        }

        return redirect()->route('informasi.index')->with('success', $flashMsg)->with('status', $flashMsg);
    }

    public function kurasiIndex()
    {
        abort_unless(Auth::user()->hasAnyRole(['bem', 'admin']), 403);

        $pengumumans = Pengumuman::pendingKurasi()->with('user')->latest()->paginate(15);
        return view('bem.kurasi.index', compact('pengumumans'));
    }

    public function kurasiApprove(Pengumuman $pengumuman)
    {
        abort_unless(Auth::user()->hasAnyRole(['bem', 'admin']), 403);

        $pengumuman->update([
            'status' => 'published',
            'disetujui_oleh_id' => Auth::id(),
        ]);

        \App\Services\NotifikasiService::kirim(
            $pengumuman->user_id,
            'Berita kegiatan Anda "' . $pengumuman->judul . '" telah disetujui BEM dan resmi diterbitkan di Pusat Informasi.'
        );

        $msg = 'Pengumuman / berita telah disetujui dan diterbitkan.';
        return redirect()->route('bem.kurasi.index')->with('success', $msg)->with('status', $msg);
    }

    public function kurasiReject(Request $request, Pengumuman $pengumuman)
    {
        abort_unless(Auth::user()->hasAnyRole(['bem', 'admin']), 403);

        $request->validate([
            'catatan_kurasi' => 'required|string',
        ]);

        $pengumuman->update([
            'status' => 'ditolak',
            'catatan_kurasi' => $request->catatan_kurasi,
        ]);

        \App\Services\NotifikasiService::kirim(
            $pengumuman->user_id,
            'Pengajuan berita "' . $pengumuman->judul . '" ditolak oleh BEM dengan catatan: ' . $request->catatan_kurasi
        );

        $msg = 'Pengumuman / berita telah ditolak.';
        return redirect()->route('bem.kurasi.index')->with('success', $msg)->with('status', $msg);
    }

    public function destroyPengumuman(Pengumuman $pengumuman)
    {
        $canDelete = Auth::user()->hasAnyRole(['bem', 'bkhm', 'admin']) || Auth::id() === $pengumuman->user_id;
        abort_unless($canDelete, 403);

        if ($pengumuman->file_lampiran && Storage::disk('local')->exists($pengumuman->file_lampiran)) {
            Storage::disk('local')->delete($pengumuman->file_lampiran);
        }

        $pengumuman->delete();
        $msg = 'Pengumuman berhasil dihapus.';
        return redirect()->route('informasi.index')->with('success', $msg)->with('status', $msg);
    }

    public function storeRegulasi(Request $request)
    {
        if (Auth::user()->roles->first()->name !== 'bpm') {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'file_path' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:10240',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            // SEC-01: file disimpan di disk privat, disajikan lewat controller.
            'file_path' => $request->file('file_path')->store('regulasi', 'local'),
        ];

        Regulasi::create($data);

        // FR-022 §22 no.8: beri tahu seluruh pengguna atas regulasi baru.
        \App\Services\NotifikasiService::kirimKeSemua('Regulasi baru: "' . $request->judul . '".');

        return redirect()->back()->with('success', 'Regulasi/UU berhasil ditambahkan.');
    }

    public function destroyRegulasi(Regulasi $regulasi)
    {
        if (Auth::user()->roles->first()->name !== 'bpm') {
            abort(403);
        }

        if ($regulasi->file_path && Storage::disk('local')->exists($regulasi->file_path)) {
            Storage::disk('local')->delete($regulasi->file_path);
        }

        $regulasi->delete();
        return redirect()->back()->with('success', 'Regulasi berhasil dihapus.');
    }

    /**
     * SEC-01: sajikan lampiran pengumuman dari disk privat (dapat diakses publik).
     */
    public function lampiranPengumuman(Pengumuman $pengumuman)
    {
        abort_if(! $pengumuman->file_lampiran, 404, 'Lampiran tidak ditemukan.');

        return $this->serveFile(
            $pengumuman->file_lampiran,
            'lampiran-pengumuman-' . $pengumuman->id . '.' . pathinfo($pengumuman->file_lampiran, PATHINFO_EXTENSION)
        );
    }

    /**
     * SEC-01: sajikan dokumen regulasi dari disk privat (dapat diakses publik).
     */
    public function unduhRegulasi(Regulasi $regulasi)
    {
        abort_if(! $regulasi->file_path, 404, 'Dokumen tidak ditemukan.');

        return $this->serveFile(
            $regulasi->file_path,
            'regulasi-' . $regulasi->id . '.' . pathinfo($regulasi->file_path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Utamakan disk privat; fallback ke disk public untuk file lama yang belum dimigrasikan.
     */
    private function serveFile(string $path, string $downloadName)
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->download($path, $downloadName);
            }
        }

        abort(404, 'Dokumen tidak ditemukan.');
    }
}
