<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKerjaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('bpm') || $user->hasRole('admin') || $user->hasRole('bkh')) {
            $prokers = ProgramKerja::with('user')->latest()->paginate(10);
        } else {
            $prokers = ProgramKerja::where('user_id', $user->id)->latest()->paginate(10);
        }
        return view('proker.index', compact('prokers'));
    }

    public function create()
    {
        return view('proker.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_proker' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'rencana_pelaksanaan' => 'required|date',
        ]);

        ProgramKerja::create([
            'user_id' => Auth::id(),
            'nama_proker' => $request->nama_proker,
            'deskripsi' => $request->deskripsi,
            'rencana_pelaksanaan' => $request->rencana_pelaksanaan,
            'status' => 'rencana',
        ]);

        return redirect()->route('proker.index')->with('success', 'Program kerja berhasil diajukan.');
    }

    public function update(Request $request, ProgramKerja $proker)
    {
        $request->validate([
            'status' => 'required|in:rencana,proses,terlaksana,kendala',
            'catatan_bpm' => 'nullable|string',
        ]);

        $proker->update([
            'status' => $request->status,
            'catatan_bpm' => $request->catatan_bpm,
        ]);

        return redirect()->route('proker.index')->with('success', 'Program kerja berhasil diperbarui.');
    }
}
