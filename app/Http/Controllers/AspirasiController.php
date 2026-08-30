<?php

namespace App\Http\Controllers;

use App\Models\Aspirasi;
use Illuminate\Http\Request;

class AspirasiController extends Controller
{
    public function index()
    {
        $aspirasis = Aspirasi::latest()->paginate(10);
        return view('aspirasi.index', compact('aspirasis'));
    }

    public function create()
    {
        return view('aspirasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengirim' => 'required|string|max:255',
            'email_pengirim' => 'nullable|email|max:255',
            'isi_aspirasi' => 'required|string',
        ]);

        Aspirasi::create($validated);

        return redirect()->back()->with('success', 'Aspirasi berhasil dikirim!');
    }

    public function show(Aspirasi $aspirasi)
    {
        return view('aspirasi.show', compact('aspirasi'));
    }

    public function update(Request $request, Aspirasi $aspirasi)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,ditindaklanjuti,selesai',
            'tanggapan' => 'nullable|string',
        ]);

        $aspirasi->update($validated);

        return redirect()->route('aspirasi.index')->with('success', 'Status aspirasi berhasil diperbarui.');
    }
}
