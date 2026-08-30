<?php

namespace App\Http\Controllers\Bkkh;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PeminjamanTempat;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BkkhController extends Controller
{
    public function saldo()
    {
        $users = User::role(['ormawa','bem','bpm'])->withCount('pengajuans as total_pengajuan')->get()->map(function($u){
            $terpakai = $u->saldo_awal - $u->saldo;
            if ($terpakai < 0) $terpakai = 0;
            $u->total_terpakai = $terpakai;
            $u->rincian = $u->pengajuans()->latest()->take(3)->pluck('nama_kegiatan')->implode(', ');
            return $u;
        });
        return view('bkkh.saldo', compact('users'));
    }

    public function arsipSurat(Request $request)
    {
        $query = Pengajuan::with(['user','state'])->whereNotNull('nomor_surat');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use($s){
                $q->where('nomor_surat','like',"%$s%")->orWhere('nama_kegiatan','like',"%$s%");
            });
        }
        $arsip = $query->latest()->paginate(10)->withQueryString();
        return view('bkkh.arsip', compact('arsip'));
    }

    public function spCreate()
    {
        $ormawas = User::role('ormawa')->get();
        return view('bkkh.sp_create', compact('ormawas'));
    }

    public function spStore(Request $request)
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
        // generate simple PDF placeholder (html printable) - actual pdf via dompdf if needed
        return redirect()->route('bkkh.arsip.index')->with('success','Surat Peringatan berhasil diterbitkan: '.$sp->nomor_surat);
    }

    public function spShow(SuratPeringatan $sp)
    {
        $sp->load(['target','creator']);
        return view('bkkh.sp_show', compact('sp'));
    }

    public function verifikasiTempat()
    {
        $antrian = PeminjamanTempat::with(['user','ruangan'])->where('status_bkkh','pending')->latest()->get();
        return view('bkkh.verifikasi_tempat', compact('antrian'));
    }
}
