<?php

namespace App\Http\Controllers;

use App\Mail\TiketDiterbitkanMail;
use App\Models\TiketLayanan;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TiketPublicController extends Controller
{
    /**
     * Portal Induk Layanan Publik Mahasiswa
     */
    public function index()
    {
        $showcasePrestasi = TiketLayanan::where('kategori', 'prestasi')
            ->where('sub_kategori', 'lapor_prestasi')
            ->where('tampil_ke_publik', true)
            ->latest()
            ->take(6)
            ->get();

        return view('public.tiket.index', compact('showcasePrestasi'));
    }

    /**
     * Form Aspirasi Mahasiswa
     */
    public function aspirasiCreate()
    {
        return view('public.tiket.aspirasi');
    }

    /**
     * Simpan Tiket Aspirasi
     */
    public function aspirasiStore(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:30',
            'nama_mahasiswa' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'nullable|string|max:25',
            'prodi' => 'nullable|string|max:100',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('tiket-lampiran', 'local');
        }

        $kodeTiket = TiketLayanan::generateKodeTiket();

        $tiket = TiketLayanan::create([
            'kode_tiket' => $kodeTiket,
            'kategori' => 'aspirasi',
            'nim' => $validated['nim'],
            'nama_mahasiswa' => $validated['nama_mahasiswa'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'prodi' => $validated['prodi'] ?? null,
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        // Kirim email kode tiket ke pengaju
        try {
            Mail::to($tiket->email)->send(new TiketDiterbitkanMail($tiket));
        } catch (\Throwable $e) {
            // Abaikan jika mail driver offline
        }

        // Notifikasi internal ke BPM
        NotifikasiService::kirimKeRole('bpm', 'Aspirasi baru masuk [Kode: ' . $kodeTiket . ']: "' . $tiket->judul . '".');

        return redirect()->route('layanan.cek-status', [
            'kode' => $kodeTiket,
            'email' => $tiket->email,
        ])->with('success', 'Aspirasi Anda berhasil dikirim! Kode Tiket: ' . $kodeTiket . '. Kode ini juga telah dikirimkan ke email Anda.');
    }

    /**
     * Form Konseling Mahasiswa (Rahasia)
     */
    public function konselingCreate()
    {
        return view('public.tiket.konseling');
    }

    /**
     * Simpan Tiket Konseling (Tertutup & Rahasia BKHM)
     */
    public function konselingStore(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:30',
            'nama_mahasiswa' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'nullable|string|max:25',
            'prodi' => 'nullable|string|max:100',
            'topik_konseling' => 'required|string|max:100',
            'metode_konseling' => 'required|string|max:50',
            'deskripsi_masalah' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('tiket-lampiran', 'local');
        }

        $kodeTiket = TiketLayanan::generateKodeTiket();

        $tiket = TiketLayanan::create([
            'kode_tiket' => $kodeTiket,
            'kategori' => 'konseling',
            'nim' => $validated['nim'],
            'nama_mahasiswa' => $validated['nama_mahasiswa'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'prodi' => $validated['prodi'] ?? null,
            'topik_konseling' => $validated['topik_konseling'],
            'metode_konseling' => $validated['metode_konseling'],
            'deskripsi_masalah' => $validated['deskripsi_masalah'],
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        try {
            Mail::to($tiket->email)->send(new TiketDiterbitkanMail($tiket));
        } catch (\Throwable $e) {
        }

        // Notifikasi tertutup hanya ke BKHM
        NotifikasiService::kirimKeRole('bkhm', 'Permohonan konseling personal baru [Kode: ' . $kodeTiket . '] masuk secara rahasia.');

        return redirect()->route('layanan.cek-status', [
            'kode' => $kodeTiket,
            'email' => $tiket->email,
        ])->with('success', 'Permohonan konseling Anda berhasil dikirim secara rahasia! Kode Tiket: ' . $kodeTiket . '. Staf BKHM akan merespons melalui sistem dan email Anda.');
    }

    /**
     * Form Prestasi & Delegasi Lomba
     */
    public function prestasiCreate()
    {
        return view('public.tiket.prestasi');
    }

    /**
     * Simpan Tiket Prestasi / Pengajuan Delegasi Lomba
     */
    public function prestasiStore(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:30',
            'nama_mahasiswa' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'nullable|string|max:25',
            'prodi' => 'nullable|string|max:100',
            'sub_kategori' => 'required|in:lapor_prestasi,pengajuan_dana_delegasi',
            'nama_kegiatan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tingkat' => 'required|string|max:50',
            'capaian' => 'nullable|string|max:100',
            'tanggal_kegiatan' => 'nullable|date',
            'estimasi_biaya' => 'nullable|numeric|min:0',
            'lampiran_bukti' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $lampiranPath = $request->file('lampiran_bukti')->store('tiket-prestasi', 'local');
        $kodeTiket = TiketLayanan::generateKodeTiket();

        $tiket = TiketLayanan::create([
            'kode_tiket' => $kodeTiket,
            'kategori' => 'prestasi',
            'sub_kategori' => $validated['sub_kategori'],
            'nim' => $validated['nim'],
            'nama_mahasiswa' => $validated['nama_mahasiswa'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'prodi' => $validated['prodi'] ?? null,
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'penyelenggara' => $validated['penyelenggara'],
            'tingkat' => $validated['tingkat'],
            'capaian' => $validated['capaian'] ?? null,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'] ?? null,
            'estimasi_biaya' => $validated['estimasi_biaya'] ?? null,
            'lampiran_bukti' => $lampiranPath,
            'status' => 'pending',
            'tampil_ke_publik' => false,
        ]);

        try {
            Mail::to($tiket->email)->send(new TiketDiterbitkanMail($tiket));
        } catch (\Throwable $e) {
        }

        NotifikasiService::kirimKeRole('bkhm', 'Pengajuan ' . ($tiket->sub_kategori === 'lapor_prestasi' ? 'Prestasi' : 'Bantuan Lomba') . ' [Kode: ' . $kodeTiket . ']: "' . $tiket->nama_kegiatan . '".');

        $pesan = $tiket->sub_kategori === 'lapor_prestasi'
            ? 'Pelaporan prestasi Anda berhasil dikirim! Kode Tiket: ' . $kodeTiket . '.'
            : 'Pengajuan bantuan delegasi lomba Anda berhasil dikirim ke BKHM! Kode Tiket: ' . $kodeTiket . '.';

        return redirect()->route('layanan.cek-status', [
            'kode' => $kodeTiket,
            'email' => $tiket->email,
        ])->with('success', $pesan);
    }

    /**
     * Halaman Cek Status Tiket Publik (Kode Tiket + Alamat Email)
     */
    public function tracking(Request $request)
    {
        $kode = trim((string) $request->input('kode'));
        $email = trim((string) $request->input('email'));
        $tiket = null;

        if ($kode && $email) {
            $tiket = TiketLayanan::where('kode_tiket', $kode)
                ->where('email', $email)
                ->first();

            if (! $tiket) {
                return view('public.tiket.tracking', [
                    'tiket' => null,
                    'searched' => true,
                    'errorMessage' => 'Tiket dengan Kode "' . $kode . '" dan Email "' . $email . '" tidak ditemukan. Pastikan data yang dimasukkan sesuai saat pengajuan.',
                ]);
            }

            return view('public.tiket.tracking', [
                'tiket' => $tiket,
                'searched' => true,
                'errorMessage' => null,
            ]);
        }

        return view('public.tiket.tracking', [
            'tiket' => null,
            'searched' => false,
            'errorMessage' => null,
        ]);
    }

    /**
     * Showcase Prestasi Publik (Hall of Fame)
     */
    public function showcasePrestasi()
    {
        $prestasis = TiketLayanan::where('kategori', 'prestasi')
            ->where('sub_kategori', 'lapor_prestasi')
            ->where('tampil_ke_publik', true)
            ->latest()
            ->paginate(12);

        return view('public.prestasi.showcase', compact('prestasis'));
    }

    /**
     * Unduh lampiran tiket publik secara privat dan aman
     */
    public function unduhLampiran(TiketLayanan $tiket, Request $request): StreamedResponse
    {
        $filePath = $tiket->lampiran ?? $tiket->lampiran_bukti;
        abort_if(! $filePath, 404, 'File lampiran tidak ditemukan.');

        // Verifikasi izin: staf berwenang ATAU pengaju yang menyertakan email di query string
        $user = auth()->user();
        $emailParam = $request->query('email');

        $isAuthorizedStaff = $user && $user->hasAnyRole(['bkhm', 'bpm', 'admin', 'wr3']);
        $isOwner = $emailParam && strtolower($emailParam) === strtolower($tiket->email);

        abort_unless($isAuthorizedStaff || $isOwner, 403, 'Akses ditolak.');

        abort_unless(Storage::disk('local')->exists($filePath), 404, 'File tidak ditemukan di server.');

        $ext = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'pdf';
        return Storage::disk('local')->download($filePath, 'lampiran-' . $tiket->kode_tiket . '.' . $ext);
    }

    /**
     * Konfirmasi jadwal temu konseling oleh mahasiswa
     */
    public function konselingKonfirmasi(Request $request, TiketLayanan $tiket)
    {
        abort_unless($tiket->kategori === 'konseling', 404);

        $validated = $request->validate([
            'email' => 'required|email',
            'konfirmasi' => 'required|in:bersedia_hadir,minta_reschedule,dibatalkan_mahasiswa',
            'catatan' => 'nullable|string|max:500',
        ]);

        if (strtolower(trim($validated['email'])) !== strtolower(trim($tiket->email))) {
            return back()->with('error', 'Alamat email tidak cocok dengan data pendaftaran tiket ini.');
        }

        $tiket->update([
            'konfirmasi_mahasiswa' => $validated['konfirmasi'],
            'catatan_konfirmasi_mahasiswa' => $validated['catatan'] ?? null,
            'konfirmasi_at' => now(),
        ]);

        $labelKonfirmasi = match($validated['konfirmasi']) {
            'bersedia_hadir' => 'Bersedia Hadir',
            'minta_reschedule' => 'Permintaan Reschedule / Ganti Jadwal',
            'dibatalkan_mahasiswa' => 'Dibatalkan oleh Mahasiswa',
            default => $validated['konfirmasi'],
        };

        \App\Services\NotifikasiService::kirimKeRole(
            'bkhm',
            'Mahasiswa mengonfirmasi sesi konseling [' . $tiket->kode_tiket . ']: ' . $labelKonfirmasi .
            ($request->filled('catatan') ? ' - Catatan: "' . $request->catatan . '"' : '')
        );

        return redirect()->route('layanan.cek-status', [
            'kode' => $tiket->kode_tiket,
            'email' => $tiket->email,
        ])->with('success', 'Konfirmasi kehadiran Anda berhasil disimpan dan diteruskan ke konselor BKHM.');
    }
}
