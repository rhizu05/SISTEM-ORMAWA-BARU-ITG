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
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
        ];

        if ($request->hasFile('file_lampiran')) {
            $data['file_lampiran'] = $request->file('file_lampiran')->store('pengumuman', 'public');
        }

        Pengumuman::create($data);

        return redirect()->back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function destroyPengumuman(Pengumuman $pengumuman)
    {
        if (Auth::user()->roles->first()->name !== 'bem') {
            abort(403);
        }

        if ($pengumuman->file_lampiran && Storage::disk('public')->exists($pengumuman->file_lampiran)) {
            Storage::disk('public')->delete($pengumuman->file_lampiran);
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
            'file_path' => 'required|file|mimes:pdf|max:10240',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'file_path' => $request->file('file_path')->store('regulasi', 'public'),
        ];

        Regulasi::create($data);

        return redirect()->back()->with('success', 'Regulasi/UU berhasil ditambahkan.');
    }

    public function destroyRegulasi(Regulasi $regulasi)
    {
        if (Auth::user()->roles->first()->name !== 'bpm') {
            abort(403);
        }

        if ($regulasi->file_path && Storage::disk('public')->exists($regulasi->file_path)) {
            Storage::disk('public')->delete($regulasi->file_path);
        }

        $regulasi->delete();
        return redirect()->back()->with('success', 'Regulasi berhasil dihapus.');
    }
}
