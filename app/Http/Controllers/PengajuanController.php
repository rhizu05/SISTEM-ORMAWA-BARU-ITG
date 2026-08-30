<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\WorkflowState;
use App\Models\HistoriStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengajuan::where('user_id', Auth::id())->with('state');

        // Filter status
        if ($request->has('status') && $request->status !== '') {
            $query->whereHas('state', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        $pengajuans = $query->latest()->paginate(10);
        $states = WorkflowState::orderBy('order_num')->get();
            
        return view('pengajuan.index', compact('pengajuans', 'states'));
    }

    public function create()
    {
        $blocking = Pengajuan::where('user_id', Auth::id())
            ->whereHas('state', fn($q)=>$q->whereNotIn('name',['draft','completed']))
            ->with('state')
            ->latest()
            ->first();
        return view('pengajuan.create', compact('blocking'));
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

    public function edit(Pengajuan $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya bisa diedit jika statusnya 'draft' atau 'rejected' (revisi)
        if (!in_array($pengajuan->state->name, ['draft', 'rejected'])) {
            return redirect()->route('pengajuan.index')->with('error', 'Hanya pengajuan Draft atau Revisi yang dapat diedit.');
        }

        return view('pengajuan.edit', compact('pengajuan'));
    }

    public function update(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($pengajuan->state->name, ['draft', 'rejected'])) {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan tidak dapat diedit pada status ini.');
        }

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'dana_diajukan' => 'required|numeric|min:0',
            'tanggal_pengajuan' => 'required|date',
            'file_proposal' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $dataToUpdate = [
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'dana_diajukan' => $validated['dana_diajukan'],
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
        ];

        if ($request->hasFile('file_proposal')) {
            // Hapus file lama
            if ($pengajuan->file_proposal && Storage::disk('public')->exists($pengajuan->file_proposal)) {
                Storage::disk('public')->delete($pengajuan->file_proposal);
            }
            
            $fileProposal = $request->file('file_proposal');
            $filename = time() . '_' . $fileProposal->getClientOriginalName();
            $dataToUpdate['file_proposal'] = $fileProposal->storeAs('proposals', $filename, 'public');
        }

        // Jika statusnya 'rejected', kembalikan ke 'draft'
        if ($pengajuan->state->name === 'rejected') {
            $draftState = WorkflowState::where('name', 'draft')->first();
            $dataToUpdate['workflow_state_id'] = $draftState->id;
            
            HistoriStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'workflow_state_id' => $draftState->id,
                'catatan' => 'Ormawa melakukan revisi dokumen.'
            ]);
        }

        $pengajuan->update($dataToUpdate);

        return redirect()->route('pengajuan.show', $pengajuan)->with('success', 'Data pengajuan berhasil diperbarui.');
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
            'catatan' => 'Pengajuan disubmit untuk diverifikasi'
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim ke BEM.');
    }
}
