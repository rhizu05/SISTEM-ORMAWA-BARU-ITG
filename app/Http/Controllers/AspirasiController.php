<?php

namespace App\Http\Controllers;

use App\Models\Aspirasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AspirasiController extends Controller
{
    public function index()
    {
        // BPM view
        $aspirasis = Aspirasi::with('user')->latest()->paginate(10);
        return view('bpm.aspirasi.index', compact('aspirasis'));
    }

    public function create()
    {
        // User view
        return view('aspirasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|string|max:100',
        ]);

        Aspirasi::create([
            'user_id' => $request->anonim ? null : Auth::id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Aspirasi berhasil dikirim!');
    }

    public function update(Request $request, Aspirasi $aspirasi)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai',
            'catatan_bpm' => 'nullable|string',
        ]);

        $aspirasi->update([
            'status' => $request->status,
            'catatan_bpm' => $request->catatan_bpm,
        ]);

        return redirect()->route('bpm.aspirasi.index')->with('success', 'Aspirasi berhasil diperbarui.');
    }
}

