<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\WorkflowState;
use App\Models\HistoriStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LpjController extends Controller
{
    public function index()
    {
        // Menampilkan daftar pengajuan yang sudah cair dan siap upload LPJ, atau sedang proses LPJ
        $pengajuans = Pengajuan::where('user_id', Auth::id())
            ->whereHas('state', function ($q) {
                $q->whereIn('name', ['funds_disbursed', 'lpj_submitted', 'completed']);
            })
            ->latest()
            ->paginate(10);
            
        return view('lpj.index', compact('pengajuans'));
    }

    public function create(Pengajuan $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403);
        }

        if ($pengajuan->state->name !== 'funds_disbursed') {
            return redirect()->route('lpj.index')->with('error', 'Status pengajuan tidak valid untuk upload LPJ.');
        }

        return view('lpj.create', compact('pengajuan'));
    }

    public function store(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id() || $pengajuan->state->name !== 'funds_disbursed') {
            abort(403);
        }

        $request->validate([
            'file_lpj' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('file_lpj')) {
            // Hapus file lama jika ada
            if ($pengajuan->file_lpj && Storage::disk('public')->exists($pengajuan->file_lpj)) {
                Storage::disk('public')->delete($pengajuan->file_lpj);
            }
            
            $fileLpj = $request->file('file_lpj');
            $filename = time() . '_LPJ_' . $fileLpj->getClientOriginalName();
            $path = $fileLpj->storeAs('lpj', $filename, 'public');

            $stateLpjSubmitted = WorkflowState::where('name', 'lpj_submitted')->first();

            $pengajuan->update([
                'file_lpj' => $path,
                'tanggal_upload_lpj' => now(),
                'workflow_state_id' => $stateLpjSubmitted->id
            ]);

            HistoriStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'workflow_state_id' => $stateLpjSubmitted->id,
                'catatan' => 'Ormawa telah mengupload Laporan Pertanggungjawaban (LPJ).'
            ]);
        }

        return redirect()->route('lpj.index')->with('success', 'File LPJ berhasil diunggah dan diajukan untuk verifikasi.');
    }
}
