<?php

namespace App\Http\Controllers;

use App\Models\Dana;
use App\Models\Pengajuan;
use App\Models\WorkflowState;
use App\Models\HistoriStatus;
use App\Models\SaldoHistori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($request, $pengajuan, $stateFundsDisbursed) {
            $user = $pengajuan->user;
            $before = (float) $user->saldo;
            $after = $before - (float) $request->nominal_cair;

            Dana::create([
                'pengajuan_id' => $pengajuan->id,
                'nominal_cair' => $request->nominal_cair,
                'tanggal_cair' => $request->tanggal_cair,
            ]);

            $user->update(['saldo' => $after]);

            SaldoHistori::create([
                'user_id' => $user->id,
                'actor_id' => Auth::id(),
                'tipe' => 'pencairan',
                'nominal_sebelum' => $before,
                'nominal_sesudah' => $after,
                'selisih' => $after - $before,
                'catatan' => $request->catatan ?? 'Dana dicairkan untuk pengajuan ' . $pengajuan->nama_kegiatan,
            ]);

            $pengajuan->update([
                'workflow_state_id' => $stateFundsDisbursed->id,
                'notif_cair_terlihat' => false,
            ]);

            HistoriStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'workflow_state_id' => $stateFundsDisbursed->id,
                'catatan' => $request->catatan ?? 'Dana telah dicairkan oleh bendahara sebesar Rp ' . number_format($request->nominal_cair, 0, ',', '.'),
            ]);
        });


        return redirect()->route('verifikasi.index')->with('success', 'Dana berhasil diproses dan dicairkan.');
    }
}
