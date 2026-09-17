<?php

namespace App\Http\Controllers;

use App\Models\Aspirasi;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AspirasiController extends Controller
{
    public function index()
    {
        // View BPM: himpun & rekap aspirasi/saran (Q-BKHM-06, Q-BPM-04).
        $aspirasis = Aspirasi::with('user')->latest()->paginate(10);
        return view('bpm.aspirasi.index', compact('aspirasis'));
    }

    public function create()
    {
        return view('aspirasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|string|max:100',
        ]);

        // FR-015: identitas/NIM tetap disimpan; anonim hanya menyembunyikan dari publik.
        $aspirasi = Aspirasi::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'status' => 'pending',
            'anonim' => (bool) $request->anonim,
        ]);

        // FR-025: notifikasi ke BPM atas aspirasi baru.
        NotifikasiService::kirimKeRole('bpm', 'Aspirasi baru masuk: "' . $aspirasi->judul . '" (kategori ' . $aspirasi->kategori . ').');

        return redirect()->back()->with('success', 'Aspirasi berhasil dikirim!');
    }

    /**
     * FR-016: pelacakan status aspirasi milik pengirim.
     */
    public function mine()
    {
        $aspirasis = Aspirasi::where('user_id', Auth::id())->latest()->paginate(10);
        return view('aspirasi.mine', compact('aspirasis'));
    }

    public function update(Request $request, Aspirasi $aspirasi)
    {
        $request->validate([
            'status' => 'required|in:pending,direkap,diproses,ditindaklanjuti,selesai,ditolak,tidak_terbukti',
            'catatan_bpm' => 'nullable|string',
        ]);

        $aspirasi->update([
            'status' => $request->status,
            'catatan_bpm' => $request->catatan_bpm,
        ]);

        // FR-025: notifikasi perkembangan ke pengirim (bila tidak anonim & punya akun).
        if ($aspirasi->user_id) {
            NotifikasiService::kirim(
                $aspirasi->user_id,
                'Aspirasi "' . $aspirasi->judul . '" diperbarui menjadi status: ' . ucfirst($request->status) . '.'
            );
        }

        return redirect()->route('bpm.aspirasi.index')->with('success', 'Aspirasi berhasil diperbarui.');
    }
}
