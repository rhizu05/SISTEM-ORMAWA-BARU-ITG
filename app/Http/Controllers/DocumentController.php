<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\User;
use App\Services\LpjDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Menyajikan file proposal secara privat dengan cek hak akses (SEC-01).
     */
    public function proposal(Pengajuan $pengajuan): StreamedResponse
    {
        $this->authorizeDocument($pengajuan, 'proposal');

        return $this->serve($pengajuan->file_proposal, 'proposal-' . $pengajuan->id . '.pdf');
    }

    /**
     * Menyajikan file LPJ secara privat dengan cek hak akses (SEC-01).
     * Secara default menyajikan dokumen lengkap terlegalisir (Lembar Pengesahan TTD Digital + Isi LPJ).
     * Mode:
     * - ?mode=asli: menyajikan berkas asli yang diunggah ormawa
     * - ?mode=pengesahan: menyajikan lembar pengesahan TTD digital 1 halaman
     */
    public function lpj(Pengajuan $pengajuan, Request $request): Response
    {
        $this->authorizeDocument($pengajuan, 'lpj');

        $disposition = $request->has('download') ? 'attachment' : 'inline';

        // 1. Mode Berkas Asli
        if ($request->query('mode') === 'asli') {
            return $this->serve($pengajuan->file_lpj, 'lpj-' . $pengajuan->id . '-asli.pdf');
        }

        // 2. Mode Lembar Pengesahan Saja
        if ($request->query('mode') === 'pengesahan') {
            $coverPdf = LpjDocumentService::generateCoverPdf($pengajuan);
            return response($coverPdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "{$disposition}; filename=\"lembar-pengesahan-lpj-{$pengajuan->id}.pdf\"",
            ]);
        }

        // 3. Default: Dokumen Lengkap Terlegalisir (Merged Cover + Berkas Asli)
        $mergedRelativePath = LpjDocumentService::getMergedLpjRelativePath($pengajuan);
        if (Storage::disk('local')->exists($mergedRelativePath)) {
            return $this->serve($mergedRelativePath, "lpj-legalisir-{$pengajuan->id}.pdf");
        }

        return $this->serve($pengajuan->file_lpj, 'lpj-' . $pengajuan->id . '.pdf');
    }

    /**
     * Menyajikan file Surat Persetujuan Prodi Peminjaman Tempat secara privat.
     */
    public function persetujuanProdiTempat(\App\Models\PeminjamanTempat $peminjaman): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user->id === $peminjaman->user_id || $user->hasAnyRole(['sarpras', 'bkhm', 'admin']), 403);

        return $this->serve($peminjaman->file_persetujuan_prodi, 'surat-prodi-tempat-' . $peminjaman->id . '.pdf');
    }

    /**
     * Menyajikan file Surat Persetujuan Prodi Peminjaman Barang secara privat.
     */
    public function persetujuanProdiBarang(\App\Models\PeminjamanBarang $peminjaman): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user->id === $peminjaman->user_id || $user->hasAnyRole(['sarpras', 'bkhm', 'admin']), 403);

        return $this->serve($peminjaman->file_persetujuan_prodi, 'surat-prodi-barang-' . $peminjaman->id . '.pdf');
    }

    /**
     * Menyajikan file bukti transfer pencairan dana secara privat (SEC-01).
     */
    public function buktiTransfer(\App\Models\Dana $dana): StreamedResponse
    {
        $user = Auth::user();
        $pengajuan = $dana->pengajuan;
        abort_unless(
            $user->id === $pengajuan->user_id || $user->hasAnyRole(['bendahara', 'bkhm', 'wr3', 'bpm', 'admin']),
            403,
            'Aksi tidak diizinkan.'
        );

        $ext = pathinfo($dana->bukti_transfer, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = 'bukti-transfer-' . $pengajuan->id . '-termin-' . $dana->termin_ke . '.' . $ext;
        return $this->serve($dana->bukti_transfer, $filename);
    }

    /**
     * Menyajikan file Surat Keputusan (SK) Ormawa secara privat dengan cek hak akses (SEC-01).
     */
    public function skOrmawa(User $user, Request $request): StreamedResponse
    {
        $currentUser = Auth::user();
        abort_unless(
            $currentUser->id === $user->id || $currentUser->hasAnyRole(['bkhm', 'wr3', 'admin', 'bem', 'bpm']),
            403,
            'Aksi tidak diizinkan.'
        );

        abort_unless($user->file_sk, 404, 'Dokumen SK belum tersedia.');

        $disposition = $request->has('download') ? 'attachment' : 'inline';
        $filename = 'SK-' . \Illuminate\Support\Str::slug($user->name) . '.pdf';

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($user->file_sk)) {
                $mimeType = Storage::disk($disk)->mimeType($user->file_sk) ?: 'application/pdf';
                return Storage::disk($disk)->response($user->file_sk, $filename, [
                    'Content-Type' => $mimeType,
                ], $disposition);
            }
        }

        abort(404, 'Berkas fisik SK tidak ditemukan di penyimpanan server.');
    }

    private function serve(?string $path, string $downloadName): StreamedResponse
    {
        abort_if(! $path, 404, 'Dokumen tidak ditemukan.');

        // Utamakan disk privat (SEC-01). Fallback ke disk public untuk file lama
        // yang belum dimigrasikan (jalankan: php artisan dokumen:migrate-private).
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                $mimeType = Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream';
                return Storage::disk($disk)->response($path, $downloadName, [
                    'Content-Type' => $mimeType,
                ], 'inline');
            }
        }

        abort(404, 'Dokumen tidak ditemukan.');
    }

    private function authorizeDocument(Pengajuan $pengajuan, string $type): void
    {
        $user = Auth::user();
        $role = $user->roles->first()?->name;

        // Pemilik pengajuan selalu boleh mengakses dokumennya sendiri.
        if ($pengajuan->user_id === $user->id) {
            return;
        }

        $allowed = match ($type) {
            // Proposal: verifikator & admin.
            'proposal' => ['bem', 'bpm', 'bkhm', 'wr3', 'bendahara', 'admin'],
            // LPJ: BKHM, WR3, BPM (monitoring), admin. Bendahara tidak memeriksa LPJ.
            'lpj' => ['bkhm', 'wr3', 'bpm', 'admin'],
            default => [],
        };

        abort_unless($user->hasAnyRole($allowed), 403, 'Aksi tidak diizinkan.');
    }
}
