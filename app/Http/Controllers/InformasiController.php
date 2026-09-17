<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Regulasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InformasiController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with('user')->latest()->get();
        $regulasi = Regulasi::with('user')->latest()->get();
        
        return view('informasi.index', compact('pengumuman', 'regulasi'));
    }

    public function storePengumuman(Request $request)
    {
        if (Auth::user()->roles->first()->name !== 'bem') {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
        ];

        if ($request->hasFile('file_lampiran')) {
            // SEC-01: file disimpan di disk privat, disajikan lewat controller.
            $data['file_lampiran'] = $request->file('file_lampiran')->store('pengumuman', 'local');
        }

        Pengumuman::create($data);

        // FR-022 §22 no.8: beri tahu seluruh pengguna atas pengumuman baru.
        \App\Services\NotifikasiService::kirimKeSemua('Pengumuman baru: "' . $request->judul . '".');

        return redirect()->back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function destroyPengumuman(Pengumuman $pengumuman)
    {
        if (Auth::user()->roles->first()->name !== 'bem') {
            abort(403);
        }

        if ($pengumuman->file_lampiran && Storage::disk('local')->exists($pengumuman->file_lampiran)) {
            Storage::disk('local')->delete($pengumuman->file_lampiran);
        }

        $pengumuman->delete();
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
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
