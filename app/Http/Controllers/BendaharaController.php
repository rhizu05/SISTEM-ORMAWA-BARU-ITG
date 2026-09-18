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
        $user = $pengajuan->user;
        $maxCair = min((float) $pengajuan->dana_diajukan, (float) $user->saldo);

        $request->validate([
            'nominal_cair' => ['required', 'numeric', 'min:1', 'max:' . $maxCair],
            'tanggal_cair' => 'required|date',
            'catatan' => 'nullable|string'
        ], [
            'nominal_cair.min' => 'Nominal pencairan minimal Rp 1.',
            'nominal_cair.max' => 'Nominal pencairan tidak boleh melebihi dana diajukan (Rp ' . number_format($pengajuan->dana_diajukan, 0, ',', '.') . ') atau sisa saldo ormawa (Rp ' . number_format($user->saldo, 0, ',', '.') . ').',
        ]);

        $stateFundsDisbursed = WorkflowState::where('name', 'funds_disbursed')->firstOrFail();

        // Verifikasi awal bahwa status pengajuan adalah to_treasurer
        if ($pengajuan->state->name !== 'to_treasurer') {
            return back()->with('error', 'Pengajuan ini belum disetujui untuk dicairkan.');
        }

        // QR-BR-03/Q-WR3-01: pencairan termin berikutnya memerlukan LPJ termin sebelumnya selesai.
        $terminKe = $pengajuan->terminBerikutnya();
        if ($terminKe > 1 && ! $this->lpjTerminSebelumnyaSelesai($pengajuan)) {
            return back()->with('error', 'Pencairan termin berikutnya menunggu LPJ dan evaluasi termin sebelumnya selesai.');
        }

        // BR-11: termin berikutnya menunggu evaluasi termin sebelumnya dinyatakan memungkinkan.
        if ($terminKe > 1 && ! $pengajuan->evaluasi_termin_ok) {
            return back()->with('error', 'Pencairan termin berikutnya menunggu evaluasi termin sebelumnya dinyatakan selesai oleh WR3/BKHM.');
        }

        try {
            DB::transaction(function () use ($request, $pengajuan, $stateFundsDisbursed, $terminKe) {
                // V1: Kunci baris pengajuan untuk mencegah double-submit / race condition konkurensi
                $lockedPengajuan = Pengajuan::where('id', $pengajuan->id)->lockForUpdate()->first();
                if ($lockedPengajuan->workflow_state_id === $stateFundsDisbursed->id || $lockedPengajuan->state->name !== 'to_treasurer') {
                    throw new \Exception('Pengajuan ini telah diproses pencairannya sebelumnya.');
                }

                // Kunci akun user untuk integritas saldo
                $lockedUser = \App\Models\User::where('id', $lockedPengajuan->user_id)->lockForUpdate()->first();
                $before = (float) $lockedUser->saldo;
                $nominalCair = (float) $request->nominal_cair;

                if ($before < $nominalCair) {
                    throw new \Exception('Saldo ormawa tidak mencukupi untuk nominal pencairan tersebut.');
                }

                $after = $before - $nominalCair;

                Dana::create([
                    'pengajuan_id' => $lockedPengajuan->id,
                    'termin_ke' => $terminKe,
                    'nominal_cair' => $nominalCair,
                    'tanggal_cair' => $request->tanggal_cair,
                    'catatan' => $request->catatan,
                ]);

                // V4: Decrement saldo atomik
                $lockedUser->decrement('saldo', $nominalCair);

                SaldoHistori::create([
                    'user_id' => $lockedUser->id,
                    'actor_id' => Auth::id(),
                    'periode_anggaran_id' => \App\Models\PeriodeAnggaran::aktif()?->id,
                    'tipe' => 'pencairan',
                    'nominal_sebelum' => $before,
                    'nominal_sesudah' => $after,
                    'selisih' => -$nominalCair,
                    'catatan' => $request->catatan ?? 'Pencairan termin ' . $terminKe . ' untuk pengajuan ' . $lockedPengajuan->nama_kegiatan,
                ]);

                $lockedPengajuan->update([
                    'workflow_state_id' => $stateFundsDisbursed->id,
                    'notif_cair_terlihat' => false,
                    // BR-11: evaluasi direset agar termin berikutnya dievaluasi ulang.
                    'evaluasi_termin_ok' => false,
                ]);

                HistoriStatus::create([
                    'pengajuan_id' => $lockedPengajuan->id,
                    'user_id' => Auth::id(),
                    'workflow_state_id' => $stateFundsDisbursed->id,
                    'catatan' => $request->catatan ?? 'Dana termin ' . $terminKe . ' telah dicairkan oleh bendahara sebesar Rp ' . number_format($nominalCair, 0, ',', '.'),
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // FR-022 §22 no.3: beri tahu pengaju bahwa dana telah dicairkan.
        \App\Services\NotifikasiService::kirim(
            $pengajuan->user_id,
            'Dana termin ' . $terminKe . ' untuk pengajuan "' . $pengajuan->nama_kegiatan . '" telah dicairkan.'
        );

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

    /**
     * Ekspor rekapitulasi data keuangan format Excel (.xlsx) resmi.
     */
    public function exportExcel(Request $request)
    {
        return \App\Services\RekapKeuanganExportService::exportExcel($request->periode_id);
    }

    /**
     * Ekspor rekapitulasi data keuangan format PDF resmi.
     */
    public function exportPdf(Request $request)
    {
        return \App\Services\RekapKeuanganExportService::exportPdf($request->periode_id);
    }
}
