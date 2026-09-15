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

        // QR-BR-03/Q-WR3-01: pencairan termin berikutnya memerlukan LPJ termin sebelumnya selesai.
        $terminKe = $pengajuan->terminBerikutnya();
        if ($terminKe > 1 && ! $this->lpjTerminSebelumnyaSelesai($pengajuan)) {
            return back()->with('error', 'Pencairan termin berikutnya menunggu LPJ dan evaluasi termin sebelumnya selesai.');
        }

        DB::transaction(function () use ($request, $pengajuan, $stateFundsDisbursed, $terminKe) {
            $user = $pengajuan->user;
            $before = (float) $user->saldo;
            $after = $before - (float) $request->nominal_cair;

            Dana::create([
                'pengajuan_id' => $pengajuan->id,
                'termin_ke' => $terminKe,
                'nominal_cair' => $request->nominal_cair,
                'tanggal_cair' => $request->tanggal_cair,
                'catatan' => $request->catatan,
            ]);

            $user->update(['saldo' => $after]);

            SaldoHistori::create([
                'user_id' => $user->id,
                'actor_id' => Auth::id(),
                'tipe' => 'pencairan',
                'nominal_sebelum' => $before,
                'nominal_sesudah' => $after,
                'selisih' => $after - $before,
                'catatan' => $request->catatan ?? 'Pencairan termin ' . $terminKe . ' untuk pengajuan ' . $pengajuan->nama_kegiatan,
            ]);

            $pengajuan->update([
                'workflow_state_id' => $stateFundsDisbursed->id,
                'notif_cair_terlihat' => false,
            ]);

            HistoriStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'workflow_state_id' => $stateFundsDisbursed->id,
                'catatan' => $request->catatan ?? 'Dana termin ' . $terminKe . ' telah dicairkan oleh bendahara sebesar Rp ' . number_format($request->nominal_cair, 0, ',', '.'),
            ]);
        });

        return redirect()->route('verifikasi.index')->with('success', 'Dana termin ' . $terminKe . ' berhasil diproses dan dicairkan.');
    }

    /**
     * Apakah LPJ untuk termin sebelumnya sudah selesai/diverifikasi.
     */
    private function lpjTerminSebelumnyaSelesai(Pengajuan $pengajuan): bool
    {
        return in_array($pengajuan->state->name, ['lpj_submitted', 'completed'], true)
            || $pengajuan->file_lpj !== null;
    }

    /**
     * FR-024: ekspor/rekapitulasi pencairan dana (CSV).
     */
    public function export()
    {
        $rows = Dana::with(['pengajuan.user'])
            ->orderBy('tanggal_cair')
            ->orderBy('id')
            ->get();

        $filename = 'rekap-pencairan-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No', 'Tanggal Cair', 'Termin', 'Nama Kegiatan', 'Pengaju', 'Nominal Dicairkan', 'Catatan']);

            foreach ($rows as $i => $d) {
                fputcsv($out, [
                    $i + 1,
                    optional($d->tanggal_cair)->format('Y-m-d'),
                    $d->termin_ke,
                    $d->pengajuan->nama_kegiatan ?? '-',
                    $d->pengajuan->user->name ?? '-',
                    number_format($d->nominal_cair, 2, '.', ''),
                    $d->catatan ?? '',
                ]);
            }

            fputcsv($out, ['', '', '', '', 'TOTAL', number_format($rows->sum('nominal_cair'), 2, '.', ''), '']);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
