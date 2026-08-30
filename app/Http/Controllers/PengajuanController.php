<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\WorkflowState;
use App\Models\HistoriStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuans = Pengajuan::where('user_id', Auth::id())
            ->with('state')
            ->latest()
            ->paginate(10);
            
        return view('pengajuan.index', compact('pengajuans'));
    }

    public function create()
    {
        return view('pengajuan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'dana_diajukan' => 'required|numeric|min:0',
            'tanggal_pengajuan' => 'required|date',
            'file_proposal' => 'required|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        $fileProposal = $request->file('file_proposal');
        $filename = time() . '_' . $fileProposal->getClientOriginalName();
        $fileProposal->storeAs('proposals', $filename, 'public');

        $draftState = WorkflowState::where('name', 'draft')->first();

        $pengajuan = Pengajuan::create([
            'user_id' => Auth::id(),
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'dana_diajukan' => $validated['dana_diajukan'],
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'file_proposal' => 'proposals/' . $filename,
            'workflow_state_id' => $draftState->id,
            'unique_code' => strtoupper(Str::random(10)),
        ]);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'workflow_state_id' => $draftState->id,
            'catatan' => 'Pengajuan draft dibuat'
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dibuat sebagai Draft.');
    }

    public function show(Pengajuan $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403);
        }

        $pengajuan->load(['state', 'histori.user', 'histori.state']);
        return view('pengajuan.show', compact('pengajuan'));
    }

    public function ajukan(Pengajuan $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403);
        }

        $draftState = WorkflowState::where('name', 'draft')->first();
        if ($pengajuan->workflow_state_id !== $draftState->id) {
            return back()->with('error', 'Hanya pengajuan berstatus draft yang bisa diajukan.');
        }

        $submittedState = WorkflowState::where('name', 'submitted')->first();
        
        $pengajuan->update([
            'workflow_state_id' => $submittedState->id
        ]);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'workflow_state_id' => $submittedState->id,
            'catatan' => 'Pengajuan disubmit ke BEM'
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim ke BEM.');
    }
}
