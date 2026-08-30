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

        $isDraft = $request->action === 'draft';

        $proposal = ProposalOtomatis::create([
            'user_id' => Auth::id(),
            'nama_kegiatan' => $request->nama_kegiatan,
            'latar_belakang' => $request->latar_belakang,
            'tujuan' => $request->tujuan,
            'sasaran' => $request->sasaran,
            'penutup' => $request->penutup,
            'status' => $isDraft ? 'draft' : 'siap_cetak',
            
            // Setting TTD dari profil user sesuai pilihan role
            'ttd_1_role' => $request->ttd_1_role ?? 'ketua',
            'ttd_1_nama' => Auth::user()->nama_ketua,
            'ttd_1_file' => Auth::user()->ttd_ketua,
            
            'ttd_2_role' => $request->ttd_2_role ?? 'sekretaris',
            'ttd_2_nama' => Auth::user()->nama_sekretaris,
            'ttd_2_file' => Auth::user()->ttd_sekretaris,

            'ttd_3_role' => $request->ttd_3_role ?? 'ketua',
            'ttd_3_nama' => Auth::user()->nama_ketua,
            'ttd_3_file' => Auth::user()->ttd_ketua,
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

        if ($request->action === 'print') {
            return redirect()->route('generator.print', $proposal)->with('success', 'Proposal berhasil disimpan dan siap dicetak.');
        }

        return redirect()->route('archive.index')->with('success', 'Draft proposal berhasil disimpan.');
    }

    public function show(ProposalOtomatis $proposal)
    {
        if ($proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin'])) {
            abort(403);
        }
        
        $proposal->load(['rab', 'panitia']);
        return view('generator.show', compact('proposal'));
    }

    public function createLetter()
    {
        return view('generator.letters.create');
    }

    public function storeLetter(Request $request)
    {
        $request->validate([
            'type' => 'required|in:undangan,tugas,permohonan,keterangan_aktif',
            'perihal' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $letter = \App\Models\Letter::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'perihal' => $request->perihal,
            'content' => $request->content,
            'metadata' => $request->only(['tujuan', 'tanggal_acara', 'masa_berlaku', 'nama_penerima']),
        ]);

        return redirect()->route('generator.letters.show', $letter)->with('success', 'Surat berhasil dibuat.');
    }

    public function showLetter(\App\Models\Letter $letter)
    {
        if ($letter->user_id !== Auth::id()) abort(403);
        return view('generator.letters.show', compact('letter'));
    }

    public function archive()
    {
        $proposals = ProposalOtomatis::where('user_id', Auth::id())->latest()->get();
        $letters = \App\Models\Letter::where('user_id', Auth::id())->latest()->get();
        
        // LPJ usually linked to Proposal, for now we show proposals that have LPJ
        return view('generator.archive', compact('proposals', 'letters'));
    }

    public function createLpj($proposalId = null)
    {
        $proposal = null;
        if ($proposalId) {
            $proposal = ProposalOtomatis::find($proposalId);
            if ($proposal && $proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin'])) {
                abort(403);
            }
        }
        
        return view('generator.lpj.create', compact('proposal'));
    }

    public function storeLpj(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string',
            'pendahuluan' => 'required|string',
            'waktu_tempat' => 'required|string',
            'hasil_kegiatan' => 'required|string',
            'hambatan' => 'required|string',
            'saran' => 'required|string',
            'penutup' => 'required|string',
        ]);

        $lpj = \App\Models\Letter::create([
            'user_id' => Auth::id(),
            'type' => 'lpj',
            'perihal' => 'Laporan Pertanggungjawaban (LPJ) - ' . $request->nama_kegiatan,
            'content' => json_encode([
                'pendahuluan' => $request->pendahuluan,
                'waktu_tempat' => $request->waktu_tempat,
                'hasil_kegiatan' => $request->hasil_kegiatan,
                'hambatan' => $request->hambatan,
                'saran' => $request->saran,
                'penutup' => $request->penutup,
                'is_draft' => $request->action === 'draft',
            ]),
            'metadata' => [
                'proposal_id' => $request->proposal_id,
                'realisasi_dana' => $request->total_realisasi ?? 0,
                'ttd_1' => $request->ttd_1,
                'ttd_2' => $request->ttd_2,
                'ttd_3' => $request->ttd_3,
                'realisasi_items' => $request->realisasi_items,
            ],
        ]);

        if ($request->action === 'print') {
            return redirect()->route('generator.lpj.show', $lpj)->with('success', 'LPJ berhasil disimpan dan siap dicetak.');
        }

        return redirect()->route('archive.index')->with('success', 'Draft LPJ berhasil disimpan.');
    }

    public function showLpj(\App\Models\Letter $lpj)
    {
        if ($lpj->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin'])) {
            abort(403);
        }
        
        $proposal = \App\Models\ProposalOtomatis::find($lpj->metadata['proposal_id'] ?? null);
        return view('generator.lpj.show', compact('lpj', 'proposal'));
    }

    public function print(ProposalOtomatis $proposal)
    {
        if ($proposal->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['bem', 'bpm', 'bkh', 'wr3', 'bendahara'])) {
            abort(403);
        }
        
        $proposal->load(['rab', 'panitia', 'user']);
        $konfig = \App\Models\Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
        
        return view('generator.print', compact('proposal', 'konfig'));
    }
}