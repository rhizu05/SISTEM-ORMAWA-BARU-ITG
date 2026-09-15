<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles->first()?->name;

        // Verifikator (BKHM/WR3/admin) & BPM melihat semua; lain hanya milik sendiri.
        if (in_array($role, ['bkhm', 'wr3', 'bpm', 'admin'], true)) {
            $prestasis = Prestasi::with('user')->latest()->paginate(10);
        } else {
            $prestasis = Prestasi::where('user_id', $user->id)->latest()->paginate(10);
        }

        return view('prestasi.index', compact('prestasis'));
    }

    public function create()
    {
        return view('prestasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'tingkat' => 'required|in:' . implode(',', Prestasi::TINGKAT),
            'juara' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'afiliasi' => 'required|in:' . implode(',', Prestasi::AFILIASI),
            'unit_terkait' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_bukti' => 'required|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
        ]);

        // FR-020/SEC-01: bukti prestasi disimpan di disk privat.
        $file = $request->file('file_bukti');
        $filename = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $validated['file_bukti'] = $file->storeAs('prestasi', $filename, 'local');
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        Prestasi::create($validated);

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil dilaporkan dan menunggu verifikasi.');
    }

    public function verify(Request $request, Prestasi $prestasi)
    {
        $role = Auth::user()->roles->first()?->name;

        if (! in_array($role, ['bkhm', 'wr3', 'admin'], true)) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:terverifikasi,ditolak',
            'catatan_bkhm' => 'nullable|string',
        ]);

        $prestasi->update([
            'status' => $request->status,
            'catatan_bkhm' => $request->catatan_bkhm,
        ]);

        // FR-025: notifikasi ke pelapor.
        \App\Services\NotifikasiService::kirim(
            $prestasi->user_id,
            'Prestasi "' . $prestasi->nama_kegiatan . '" ' . ($request->status === 'terverifikasi' ? 'telah diverifikasi.' : 'ditolak.'),
        );

        return redirect()->back()->with('success', 'Status prestasi berhasil diperbarui.');
    }

    /**
     * Unduh bukti prestasi (privat, cek hak akses).
     */
    public function bukti(Prestasi $prestasi)
    {
        $user = Auth::user();
        $role = $user->roles->first()?->name;

        $allowed = $prestasi->user_id === $user->id
            || in_array($role, ['bkhm', 'wr3', 'bpm', 'admin'], true);

        abort_unless($allowed, 403, 'Aksi tidak diizinkan.');
        abort_if(! $prestasi->file_bukti || ! Storage::disk('local')->exists($prestasi->file_bukti), 404);

        return Storage::disk('local')->download($prestasi->file_bukti, 'bukti-' . $prestasi->id . '.' . pathinfo($prestasi->file_bukti, PATHINFO_EXTENSION));
    }
}
