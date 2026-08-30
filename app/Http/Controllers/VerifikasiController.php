<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\WorkflowTransition;
use App\Models\HistoriStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->roles->first()->name;
        
        // Find all transitions allowed for this user's role
        $allowedTransitions = WorkflowTransition::where('required_role', $userRole)->get();
        $allowedStateIds = $allowedTransitions->pluck('from_state_id')->unique();
        
        // Get all pengajuan that are currently in a state that this user can action
        $pengajuans = Pengajuan::whereIn('workflow_state_id', $allowedStateIds)
            ->with(['user', 'state'])
            ->latest()
            ->paginate(10);
            
        return view('verifikasi.index', compact('pengajuans'));
    }

    public function show(Pengajuan $pengajuan)
    {
        $userRole = Auth::user()->roles->first()->name;
        
        $pengajuan->load(['user', 'state', 'histori.user', 'histori.state']);
        
        // Get transitions available for this specific state AND this user's role
        $availableTransitions = WorkflowTransition::where('from_state_id', $pengajuan->workflow_state_id)
            ->where('required_role', $userRole)
            ->get();
            
        return view('verifikasi.show', compact('pengajuan', 'availableTransitions'));
    }

    public function process(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'transition_id' => 'required|exists:workflow_transitions,id',
            'catatan' => 'nullable|string'
        ]);

        $transition = WorkflowTransition::findOrFail($request->transition_id);
        $userRole = Auth::user()->roles->first()->name;

        // Verify the transition is valid for the current state and user's role
        if ($transition->from_state_id !== $pengajuan->workflow_state_id || $transition->required_role !== $userRole) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Apply transition
        $pengajuan->update([
            'workflow_state_id' => $transition->to_state_id
        ]);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'workflow_state_id' => $transition->to_state_id,
            'catatan' => $request->catatan ?? 'Status diubah: ' . $transition->action_label
        ]);

        return redirect()->route('verifikasi.index')->with('success', 'Pengajuan berhasil diproses.');
    }
}
