<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\JadwalKuliah;
use App\Models\MasterRuangan;
use Illuminate\Http\Request;

class JadwalKuliahController extends Controller
{
    public function index()
    {
        $jadwals = JadwalKuliah::with('ruangan')->orderBy('hari')->orderBy('jam_mulai')->paginate(15);
        $ruangans = MasterRuangan::orderBy('nama_ruangan')->get();

        return view('sarpras.jadwal.index', compact('jadwals', 'ruangans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:master_ruangan,id',
            'hari' => 'required|integer|between:1,7',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'mata_kuliah' => 'required|string|max:255',
            'semester' => 'nullable|string|max:100',
        ]);

        // Q-SAR-02: cek bentrok dengan jadwal kuliah lain di ruangan & hari yang sama.
        $bentrok = JadwalKuliah::where('ruangan_id', $validated['ruangan_id'])
            ->where('hari', $validated['hari'])
            ->where('aktif', true)
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai'])
            ->exists();

        if ($bentrok) {
            return back()->withInput()->with('error', 'Jadwal bertabrakan dengan mata kuliah lain di ruangan & hari yang sama.');
        }

        JadwalKuliah::create($validated + ['aktif' => true]);

        return redirect()->back()->with('success', 'Jadwal kuliah berhasil ditambahkan.');
    }

    public function destroy(JadwalKuliah $jadwal)
    {
        $jadwal->delete();
        return redirect()->back()->with('success', 'Jadwal kuliah berhasil dihapus.');
    }
}
