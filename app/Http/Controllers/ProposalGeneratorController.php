<?php

namespace App\Http\Controllers;

use App\Models\ProposalOtomatis;
use App\Models\ProposalPanitia;
use App\Models\ProposalRab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalGeneratorController extends Controller
{
    public function index()
    {
        $proposals = ProposalOtomatis::where('user_id', Auth::id())->latest()->get();
        return view('generator.index', compact('proposals'));
    }

    public function create()
    {
        return view('generator.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'latar_belakang' => 'required|string',
            'tujuan' => 'required|string',
            'sasaran' => 'required|string',
            'penutup' => 'required|string',
        ]);

        $proposal = ProposalOtomatis::create([
            'user_id' => Auth::id(),
            'nama_kegiatan' => $request->nama_kegiatan,
            'latar_belakang' => $request->latar_belakang,
            'tujuan' => $request->tujuan,
            'sasaran' => $request->sasaran,
            'penutup' => $request->penutup,
            
            // Setting default TTD dari profil user
            'ttd_1_role' => 'ketua',
            'ttd_1_nama' => Auth::user()->nama_ketua,
            'ttd_1_file' => Auth::user()->ttd_ketua,
            
            'ttd_2_role' => 'sekretaris',
            'ttd_2_nama' => Auth::user()->nama_sekretaris,
            'ttd_2_file' => Auth::user()->ttd_sekretaris,
        ]);

        // Save RAB
        if ($request->has('rab_rincian')) {
            foreach ($request->rab_rincian as $key => $rincian) {
                if (!empty($rincian)) {
                    $vol = $request->rab_vol[$key] ?? 0;
                    $harga = $request->rab_harga[$key] ?? 0;
                    
                    ProposalRab::create([
                        'proposal_id' => $proposal->id,
                        'rincian' => $rincian,
                        'volume' => $vol,
                        'satuan' => $request->rab_sat[$key] ?? 'Ls',
                        'harga_satuan' => $harga,
                        'total_harga' => $vol * $harga
                    ]);
                }
            }
        }

        // Save Panitia
        if ($request->has('pan_jabatan')) {
            foreach ($request->pan_jabatan as $key => $jabatan) {
                if (!empty($jabatan) && !empty($request->pan_nama[$key])) {
                    ProposalPanitia::create([
                        'proposal_id' => $proposal->id,
                        'jabatan' => $jabatan,
                        'nama_mahasiswa' => $request->pan_nama[$key],
                        'nim' => $request->pan_nim[$key] ?? null
                    ]);
                }
            }
        }

        return redirect()->route('generator.show', $proposal)->with('success', 'Draft Proposal berhasil dibuat.');
    }

    public function show(ProposalOtomatis $proposal)
    {
        if ($proposal->user_id !== Auth::id()) abort(403);
        
        $proposal->load(['rab', 'panitia']);
        return view('generator.show', compact('proposal'));
    }

    // Karena DomPDF error di environment Windows/Composer saat ini, kita gunakan tampilan HTML printable
    public function print(ProposalOtomatis $proposal)
    {
        if ($proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkh', 'wr3', 'bendahara'])) {
            abort(403);
        }
        
        $proposal->load(['rab', 'panitia', 'user']);
        
        // Ambil konfigurasi kop surat
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        
        return view('generator.print', compact('proposal', 'konfig'));
    }
}
