<?php

namespace App\Http\Controllers\Bpm;

use App\Http\Controllers\Controller;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpController extends Controller
{
    public function create()
    {
        $ormawas = User::role(['ormawa', 'bem'])->get();
        return view('bpm.sp.create', compact('ormawas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_user_id'=>'required|exists:users,id',
            'nomor_surat'=>'required|string|unique:surat_peringatans,nomor_surat',
            'tingkat'=>'required|in:SP-1,SP-2,SP-3',
            'perihal'=>'required|string',
            'alasan_singkat'=>'required|string',
            'deskripsi'=>'required|string',
            'sanksi'=>'required|string',
            'tanggal_surat'=>'required|date',
            'penandatangan'=>'required|string',
        ]);

        $sp = SuratPeringatan::create([
            'target_user_id'=>$request->target_user_id,
            'nomor_surat'=>$request->nomor_surat,
            'tingkat'=>$request->tingkat,
            'perihal'=>$request->perihal,
            'alasan_singkat'=>$request->alasan_singkat,
            'deskripsi'=>$request->deskripsi,
            'sanksi'=>$request->sanksi,
            'tanggal_surat'=>$request->tanggal_surat,
            'penandatangan'=>$request->penandatangan,
            'created_by'=>Auth::id(),
        ]);

        return redirect()->route('bpm.dashboard')->with('success','Surat Peringatan berhasil diterbitkan: '.$sp->nomor_surat);
    }

    public function show(SuratPeringatan $sp)
    {
        $sp->load(['target','creator']);
        return view('bkkh.sp_show', compact('sp'));
    }
}
