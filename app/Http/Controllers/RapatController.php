<?php

namespace App\Http\Controllers;

use App\Models\JadwalRapat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RapatController extends Controller
{
    public function index()
    {
        $role = Auth::user()->roles->first()->name;
        
        // Ormawa hanya melihat rapat yang target_peserta ada 'ormawa' atau all
        if ($role === 'ormawa') {
            $rapats = JadwalRapat::whereJsonContains('target_peserta', 'ormawa')
                ->orWhereJsonContains('target_peserta', 'all')
                ->with('penyelenggara')
                ->orderBy('tanggal_rapat', 'desc')
                ->orderBy('jam_rapat', 'desc')
                ->get();
        } else {
            // Role lain bisa melihat semua atau sesuai role-nya
            $rapats = JadwalRapat::with('penyelenggara')
                ->orderBy('tanggal_rapat', 'desc')
                ->orderBy('jam_rapat', 'desc')
                ->get();
        }

        return view('rapat.index', compact('rapats'));
    }

    public function store(Request $request)
    {
        // Hanya BEM dan BPM yang bisa buat jadwal rapat
        if (!in_array(Auth::user()->roles->first()->name, ['bem', 'bpm'])) {
            abort(403);
        }

        $request->validate([
            'judul_rapat' => 'required|string|max:255',
            'tanggal_rapat' => 'required|date',
            'jam_rapat' => 'required',
            'lokasi' => 'required|string|max:255',
            'link_meeting' => 'nullable|url|max:255',
            'deskripsi' => 'nullable|string',
            'peserta' => 'required|array|min:1',
        ]);

        JadwalRapat::create([
            'user_id' => Auth::id(),
            'judul_rapat' => $request->judul_rapat,
            'tanggal_rapat' => $request->tanggal_rapat,
            'jam_rapat' => $request->jam_rapat,
            'lokasi' => $request->lokasi,
            'link_meeting' => $request->link_meeting,
            'deskripsi' => $request->deskripsi,
            'target_peserta' => $request->peserta,
        ]);

        return redirect()->back()->with('success', 'Jadwal rapat berhasil ditambahkan.');
    }

    public function destroy(JadwalRapat $rapat)
    {
        // Hanya penyelenggara yang bisa hapus
        if ($rapat->user_id !== Auth::id()) {
            abort(403);
        }

        $rapat->delete();
        return redirect()->back()->with('success', 'Jadwal rapat berhasil dibatalkan/dihapus.');
    }
}
