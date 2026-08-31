<?php

namespace App\Http\Controllers;

use App\Models\Regulasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RegulasiController extends Controller
{
    public function index()
    {
        $regulasis = Regulasi::latest()->get();
        return view('bpm.regulasi.index', compact('regulasis'));
    }

    public function create()
    {
        return view('bpm.regulasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:Undang-Undang,Pengumuman,Pedoman',
            'deskripsi' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tanggal_terbit' => 'required|date',
        ]);

        $filePath = $request->file('file')->store('regulasis', 'public');

        Regulasi::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'file_path' => $filePath,
            'tanggal_terbit' => $request->tanggal_terbit,
        ]);

        return redirect()->route('bpm.regulasi.index')->with('success', 'Regulasi berhasil diterbitkan.');
    }

    public function destroy(Regulasi $regulasi)
    {
        Storage::disk('public')->delete($regulasi->file_path);
        $regulasi->delete();
        return back()->with('success', 'Regulasi berhasil dihapus.');
    }
}
