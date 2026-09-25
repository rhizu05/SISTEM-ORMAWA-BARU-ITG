<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use App\Models\HistoriStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->roles->first()->name;

        if ($userRole === 'admin') {
            $pengajuans = Pengajuan::with(['user', 'state', 'programKerja'])->latest()->paginate(10);
            return view('verifikasi.index', compact('pengajuans'));
        }

        // Find all transitions allowed for this user's role
        $allowedTransitions = WorkflowTransition::where('required_role', $userRole)->get();
        $allowedStateIds = $allowedTransitions->pluck('from_state_id')->unique();

        // KRITIS-1 Opsi C: antrean verifikator TIDAK BOLEH menampilkan pengajuan berstate
        // `draft`. Draft adalah milik pengaju (disubmit via `PengajuanController::ajukan`),
        // bukan domain verifikator. Membiarkannya tampil membocorkan daftar draft pengaju
        // lain dan, sebelum patch A, memungkinkan transisi `draft -> ...` diterapkan dari
        // antrean verifikator — melompati tahap BR-03/BR-14.
        $draftStateId = WorkflowState::where('name', 'draft')->value('id');
        if ($draftStateId) {
            $allowedStateIds = $allowedStateIds->reject($draftStateId);
        }

        // Get all pengajuan that are currently in a state that this user can action
        $pengajuans = Pengajuan::whereIn('workflow_state_id', $allowedStateIds)
            ->with(['user', 'state', 'programKerja'])
            ->latest()
            ->paginate(10);
            
        return view('verifikasi.index', compact('pengajuans'));
    }

    public function show(Pengajuan $pengajuan)
    {
        $userRole = Auth::user()->roles->first()->name;
        
        $pengajuan->load(['user', 'state', 'histori.user', 'histori.state', 'programKerja']);

        if ($userRole === 'admin') {
            $availableTransitions = collect();
            return view('verifikasi.show', compact('pengajuan', 'availableTransitions'));
        }
        
        // Get transitions available for this specific state AND this user's role
        $availableTransitions = WorkflowTransition::where('from_state_id', $pengajuan->workflow_state_id)
            ->where('required_role', $userRole)
            ->with(['fromState', 'toState'])
            ->get();

        // KRITIS-1 Opsi C: transisi yang berangkat dari state `draft` (submit/ajukan)
        // adalah milik pengaju, BUKAN verifikator. Jangan ekspos tombolnya kepada
        // verifikator yang bukan pemilik pengajuan — meskipun secara teknis role-nya
        // punya transisi tersebut (bem/bpm juga bisa menjadi pengaju).
        $availableTransitions = $availableTransitions->reject(
            fn ($t) => $t->fromState->name === 'draft' && $pengajuan->user_id !== Auth::id()
        );

        return view('verifikasi.show', compact('pengajuan', 'availableTransitions'));
    }

    public function process(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'transition_id' => 'required|exists:workflow_transitions,id',
            'catatan' => 'nullable|string',
            'nomor_surat' => 'nullable|string|max:255'
        ]);

        $transition = WorkflowTransition::with(['fromState', 'toState'])->findOrFail($request->transition_id);
        $userRole = Auth::user()->roles->first()->name;

        // Verify the transition is valid for the current state and user's role
        if ($transition->from_state_id !== $pengajuan->workflow_state_id || $transition->required_role !== $userRole) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // KRITIS-1 Opsi A: transisi yang berangkat dari state `draft` (submit/ajukan)
        // hanya boleh dipakai oleh PEMILIK pengajuan. Role bem/bpm secara teknis punya
        // transisi `draft -> ...` karena mereka juga bisa menjadi pengaju; guard ini
        // mencegah mereka memakai transisi itu terhadap draft milik pengaju lain, yang
        // akan melompati tahap verifikasi berjenjang (BR-03 / BR-14).
        if ($transition->fromState->name === 'draft' && $pengajuan->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Jika menolak atau revisi (termasuk revisi proposal ke draft atau revisi LPJ ke funds_disbursed), catatan wajib diisi
        $isRejecting = in_array($transition->toState->name, ['rejected', 'draft']) 
            || ($transition->toState->name === 'funds_disbursed' && str_contains(strtolower($transition->action_label), 'revisi'));
        if ($isRejecting && empty($request->catatan)) {
            return back()->withInput()->with('error', 'Catatan wajib diisi jika menolak atau merevisi pengajuan.');
        }

        // BKHM Tahap 1 (bpm_approved → bkhm_approved): nomor surat wajib diisi
        // sebelum meneruskan ke WR3. Target mengikuti nama state pada WorkflowSeeder.
        if ($userRole === 'bkhm'
            && $transition->toState->name === 'bkhm_approved'
            && ! $pengajuan->nomor_surat
            && ! $request->nomor_surat) {
            return back()->with('error', 'Nomor surat wajib diisi sebelum meneruskan ke WR3.');
        }

        // Apply transition
        $dataToUpdate = [
            'workflow_state_id' => $transition->to_state_id
        ];

        // FR-009: revisi/tolak dikembalikan ke pengusul; simpan titik penolakan
        // agar pengajuan ulang kembali ke tahap yang menolak.
        if ($isRejecting) {
            $dataToUpdate['rejected_from_state_id'] = $pengajuan->workflow_state_id;
        } else {
            $dataToUpdate['rejected_from_state_id'] = null;
        }

        // BR-11: verifikasi LPJ oleh WR3 sekaligus menandai evaluasi termin selesai,
        // sehingga pencairan termin berikutnya memungkinkan.
        if ($userRole === 'wr3' && $transition->toState->name === 'completed') {
            $dataToUpdate['evaluasi_termin_ok'] = true;
        }

        // Save nomor_surat if provided (BKHM)
        if ($request->filled('nomor_surat')) {
            $dataToUpdate['nomor_surat'] = $request->nomor_surat;
        }

        $pengajuan->update($dataToUpdate);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'workflow_state_id' => $transition->to_state_id,
            'catatan' => $request->catatan ?? 'Status diubah: ' . $transition->action_label,
            'catatan_kendala' => $isRejecting ? ($request->catatan ?? 'Pengajuan dikembalikan/ditolak pada tahap ' . $transition->toState->label) : null
        ]);

        // FR-025: notifikasi ke pengaju atas perubahan status.
        \App\Services\NotifikasiService::kirim(
            $pengajuan->user_id,
            'Pengajuan "' . $pengajuan->nama_kegiatan . '" kini berstatus: ' . $transition->toState->label . '.'
        );

        // FR-022 §22 no.9: penolakan oleh lembaga juga diberitahukan ke akun BEM.
        if ($isRejecting && $pengajuan->user && $pengajuan->user->hasRole('ormawa')) {
            \App\Services\NotifikasiService::kirimKeRole(
                'bem',
                'Proposal "' . $pengajuan->nama_kegiatan . '" ditolak/dikembalikan pada tahap '
                    . ($transition->fromState->label ?? 'verifikasi') . '.'
            );
        }

        return redirect()->route('verifikasi.index')->with('success', 'Pengajuan berhasil diproses.');
    }

    /**
     * BR-11: tandai evaluasi termin selesai agar pencairan termin berikutnya memungkinkan.
     */
    public function evaluasiTermin(Pengajuan $pengajuan)
    {
        $role = Auth::user()->roles->first()->name;

        if (! in_array($role, ['wr3', 'bkhm', 'admin'], true)) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($pengajuan->state->name !== 'funds_disbursed') {
            return back()->with('error', 'Evaluasi termin hanya dapat ditandai setelah dana termin sebelumnya dicairkan.');
        }

        $pengajuan->update(['evaluasi_termin_ok' => true]);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'workflow_state_id' => $pengajuan->workflow_state_id,
            'catatan' => 'Evaluasi termin ditandai selesai (BR-11).',
        ]);

        return back()->with('success', 'Evaluasi termin ditandai selesai.');
    }
}
