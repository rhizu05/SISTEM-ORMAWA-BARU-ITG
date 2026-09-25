<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\ProgramKerja;
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
        $query = Auth::user()->hasRole('admin')
            ? Pengajuan::with(['state', 'programKerja'])
            : Pengajuan::where('user_id', Auth::id())->with(['state', 'programKerja']);

        // Filter status
        if ($request->has('status') && $request->status !== '') {
            $query->whereHas('state', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        // Search keyword (nama kegiatan)
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where('nama_kegiatan', 'like', "%{$keyword}%");
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();
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
        $prokers = ProgramKerja::where('user_id', Auth::id())->orderBy('nama_proker')->get();
        return view('pengajuan.create', compact('blocking', 'prokers'));
    }

    public function store(Request $request)
    {
        $blocking = Pengajuan::where('user_id', Auth::id())
            ->whereHas('state', fn($q)=>$q->whereNotIn('name',['draft','completed']))
            ->with('state')->latest()->first();
        if ($blocking) {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan ditangguhkan: masih ada '.$blocking->nama_kegiatan.' ('.$blocking->state->label.') yang belum selesai.');
        }

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'dana_diajukan' => 'required|numeric|min:1',
            'tanggal_pengajuan' => 'required|date',
            'tanggal_mulai_kegiatan' => 'nullable|date',
            'tanggal_selesai_kegiatan' => 'nullable|date|after_or_equal:tanggal_mulai_kegiatan',
            'program_kerja_id' => 'nullable|exists:program_kerjas,id',
            'file_proposal' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:5120', // SEC-02: validasi ekstensi + MIME
        ]);

        // BR-04: nominal pengajuan tidak boleh melebihi sisa saldo pengaju.
        if ($error = $this->validasiBatasSaldo((float) $validated['dana_diajukan'])) {
            return back()->withInput()->withErrors(['dana_diajukan' => $error]);
        }

        // SEC-01: simpan di disk privat (bukan public)
        $fileProposal = $request->file('file_proposal');
        $filename = time() . '_' . Str::slug(pathinfo($fileProposal->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
        $path = $fileProposal->storeAs('proposals', $filename, 'local');

        $draftState = WorkflowState::where('name', 'draft')->first();

        $pengajuan = Pengajuan::create([
            'user_id' => Auth::id(),
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'dana_diajukan' => $validated['dana_diajukan'],
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'tanggal_mulai_kegiatan' => $validated['tanggal_mulai_kegiatan'] ?? null,
            'tanggal_selesai_kegiatan' => $validated['tanggal_selesai_kegiatan'] ?? null,
            'program_kerja_id' => $validated['program_kerja_id'] ?? null,
            'file_proposal' => $path,
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
        if ($pengajuan->user_id !== Auth::id() && ! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $pengajuan->load(['state', 'histori.user', 'histori.state', 'programKerja']);
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

        $prokers = ProgramKerja::where('user_id', Auth::id())->orderBy('nama_proker')->get();
        return view('pengajuan.edit', compact('pengajuan', 'prokers'));
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
            'dana_diajukan' => 'required|numeric|min:1',
            'tanggal_pengajuan' => 'required|date',
            'tanggal_mulai_kegiatan' => 'nullable|date',
            'tanggal_selesai_kegiatan' => 'nullable|date|after_or_equal:tanggal_mulai_kegiatan',
            'program_kerja_id' => 'nullable|exists:program_kerjas,id',
            'file_proposal' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:5120', // SEC-02
        ]);

        // BR-04: nominal pengajuan tidak boleh melebihi sisa saldo pengaju.
        if ($error = $this->validasiBatasSaldo((float) $validated['dana_diajukan'])) {
            return back()->withInput()->withErrors(['dana_diajukan' => $error]);
        }

        $dataToUpdate = [
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'dana_diajukan' => $validated['dana_diajukan'],
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'tanggal_mulai_kegiatan' => $validated['tanggal_mulai_kegiatan'] ?? null,
            'tanggal_selesai_kegiatan' => $validated['tanggal_selesai_kegiatan'] ?? null,
            'program_kerja_id' => $validated['program_kerja_id'] ?? null,
        ];

        if ($request->hasFile('file_proposal')) {
            // Hapus file lama
            if ($pengajuan->file_proposal && Storage::disk('local')->exists($pengajuan->file_proposal)) {
                Storage::disk('local')->delete($pengajuan->file_proposal);
            }

            // SEC-01: simpan di disk privat
            $fileProposal = $request->file('file_proposal');
            $filename = time() . '_' . Str::slug(pathinfo($fileProposal->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $dataToUpdate['file_proposal'] = $fileProposal->storeAs('proposals', $filename, 'local');
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

        $otherBlocking = Pengajuan::where('user_id', Auth::id())->where('id','!=',$pengajuan->id)
            ->whereHas('state', fn($q)=>$q->whereNotIn('name',['draft','completed']))->with('state')->latest()->first();
        if ($otherBlocking) {
            return back()->with('error', 'Pengajuan ditangguhkan: masih ada '.$otherBlocking->nama_kegiatan.' ('.$otherBlocking->state->label.') yang belum selesai.');
        }

        $draftState = WorkflowState::where('name', 'draft')->first();
        if ($pengajuan->workflow_state_id !== $draftState->id) {
            return back()->with('error', 'Hanya pengajuan berstatus draft yang bisa diajukan.');
        }

        // BR-13 & Gambaran MD §3.1: ketika revisi diajukan kembali, alur verifikasi
        // di-reset kembali ke tahap paling awal sesuai jenis pengaju:
        // - Ormawa/HIMA/UKM -> BEM (submitted)
        // - BEM             -> BPM (bem_approved)
        // - BPM             -> BKHM (bpm_approved)
        $isRevisi = (bool) $pengajuan->rejected_from_state_id;

        $role = Auth::user()->roles->first()->name;
        [$targetStateName, $flashMessage] = match ($role) {
            'bem' => ['bem_approved', $isRevisi ? 'Revisi berhasil diajukan kembali ke BPM (alur di-reset ke awal).' : 'Pengajuan berhasil dikirim ke BPM.'],
            'bpm' => ['bpm_approved', $isRevisi ? 'Revisi berhasil diajukan kembali ke BKHM (alur di-reset ke awal).' : 'Pengajuan berhasil dikirim ke BKHM.'],
            default => ['submitted', $isRevisi ? 'Revisi berhasil diajukan kembali ke BEM (alur di-reset ke awal).' : 'Pengajuan berhasil dikirim ke BEM.'],
        };

        $targetState = WorkflowState::where('name', $targetStateName)->first();

        $pengajuan->update([
            'workflow_state_id' => $targetState->id,
            'rejected_from_state_id' => null,
        ]);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'workflow_state_id' => $targetState->id,
            'catatan' => $isRevisi ? 'Revisi diajukan kembali (alur di-reset ke tahap awal): ' . $targetState->label : 'Pengajuan disubmit untuk diverifikasi'
        ]);

        $this->notifikasiAntrean($targetStateName, $pengajuan->nama_kegiatan);

        return redirect()->route('pengajuan.index')->with('success', $flashMessage);
    }

    /**
     * FR-022 §22 no.2: beri tahu verifikator saat pengajuan masuk antrean mereka.
     */
    private function notifikasiAntrean(string $stateName, string $namaKegiatan): void
    {
        $role = match ($stateName) {
            'submitted' => 'bem',
            'bem_approved' => 'bpm',
            'bpm_approved' => 'bkhm',
            'bkhm_approved' => 'wr3',
            'wr3_approved', 'to_treasurer' => 'bendahara',
            default => null,
        };

        if ($role) {
            \App\Services\NotifikasiService::kirimKeRole(
                $role,
                'Pengajuan "' . $namaKegiatan . '" masuk antrean verifikasi Anda.'
            );
        }
    }

    /**
     * BR-04: batas nominal pengajuan terhadap sisa saldo pengaju.
     * Mengembalikan pesan error bila melebihi, atau null bila valid.
     */
    private function validasiBatasSaldo(float $nominal): ?string
    {
        $saldo = (float) Auth::user()->saldo;

        if ($nominal > $saldo) {
            return 'Dana yang diajukan (Rp ' . number_format($nominal, 0, ',', '.')
                . ') melebihi sisa saldo Anda (Rp ' . number_format($saldo, 0, ',', '.') . ').';
        }

        return null;
    }
}
