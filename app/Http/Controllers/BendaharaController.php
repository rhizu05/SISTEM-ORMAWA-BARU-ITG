<?php

namespace App\Http\Controllers;

use App\Models\Dana;
use App\Models\Pengajuan;
use App\Models\WorkflowState;
use App\Models\HistoriStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BendaharaController extends Controller
{
    public function proses(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'nominal_cair' => 'required|numeric|min:0',
            'tanggal_cair' => 'required|date',
            'catatan' => 'nullable|string'
        ]);

        $stateFundsDisbursed = WorkflowState::where('name', 'funds_disbursed')->first();

        // Verifikasi bahwa status pengajuan adalah to_treasurer
        if ($pengajuan->state->name !== 'to_treasurer') {
            return back()->with('error', 'Pengajuan ini belum disetujui untuk dicairkan.');
        }

        // Catat dana cair
        Dana::create([
            'pengajuan_id' => $pengajuan->id,
            'nominal_cair' => $request->nominal_cair,
            'tanggal_cair' => $request->tanggal_cair,
        ]);

        // Update status pengajuan
        $pengajuan->update([
            'workflow_state_id' => $stateFundsDisbursed->id,
            'notif_cair_terlihat' => false
        ]);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'workflow_state_id' => $stateFundsDisbursed->id,
            'catatan' => $request->catatan ?? 'Dana telah dicairkan oleh bendahara sebesar Rp ' . number_format($request->nominal_cair, 0, ',', '.')
        ]);

        return redirect()->route('verifikasi.index')->with('success', 'Dana berhasil diproses dan dicairkan.');
    }
}
