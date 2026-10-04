<?php

namespace App\Services;

use App\Models\Konfigurasi;
use App\Models\Letter;
use App\Models\Pengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class LpjDocumentService
{
    /**
     * Menemukan dokumen Letter LPJ yang relevan dengan Pengajuan ini (jika ada).
     */
    public static function resolveLpjLetter(Pengajuan $pengajuan): ?Letter
    {
        // 1. Cek jika ada Letter type lpj yang secara eksplisit menunjuk proposal_otomatis_id / pengajuan_id
        $letter = Letter::where('type', 'lpj')
            ->where(function ($q) use ($pengajuan) {
                $q->whereJsonContains('metadata->pengajuan_id', $pengajuan->id)
                  ->orWhereJsonContains('metadata->proposal_id', $pengajuan->id);
            })
            ->first();

        if ($letter) {
            return $letter;
        }

        // 2. Cek Letter type lpj milik user pengajuan yang judul/perihalnya memuat nama kegiatan
        $letter = Letter::where('type', 'lpj')
            ->where('user_id', $pengajuan->user_id)
            ->where(function ($q) use ($pengajuan) {
                $q->where('perihal', 'like', '%' . $pengajuan->nama_kegiatan . '%')
                  ->orWhere('perihal', 'like', '%' . substr($pengajuan->nama_kegiatan, 0, 20) . '%');
            })
            ->latest()
            ->first();

        if ($letter) {
            return $letter;
        }

        // 3. Fallback pencocokan kata kunci kegiatan pada perihal letter lpj
        $words = array_values(array_filter(explode(' ', $pengajuan->nama_kegiatan), fn($w) => strlen($w) > 4));
        if (!empty($words)) {
            $query = Letter::where('type', 'lpj');
            foreach (array_slice($words, 0, 2) as $word) {
                $query->where('perihal', 'like', "%{$word}%");
            }
            $letter = $query->latest()->first();
            if ($letter) {
                return $letter;
            }
        }

        return null;
    }

    /**
     * Menghasilkan konten biner PDF Lembar Pengesahan LPJ ITG resmi (1 halaman A4).
     */
    public static function generateCoverPdf(Pengajuan $pengajuan): string
    {
        $pengajuan->loadMissing(['user', 'tandaTanganDigitals', 'state', 'dana']);

        $pdf = Pdf::loadView('lpj.lembar_pengesahan_pdf', [
            'pengajuan' => $pengajuan,
        ])->setPaper('a4', 'portrait');

        return $pdf->output();
    }

    /**
     * Menggabungkan atau me-render dokumen LPJ ITG resmi dengan tanda tangan digital terintegrasi.
     * 
     * 1. Jika dokumen LPJ berasal dari template sistem (Letter generator):
     *    Tanda tangan digital (Ormawa, BKHM, WR3) langsung tertera secara native di blok tanda tangan K. Penutup.
     * 2. Jika dokumen LPJ adalah berkas PDF luar yang diupload mahasiswa:
     *    Lembar Pengesahan dan Legalisir diletakkan di HALAMAN TERAKHIR sebagai penutup resmi.
     */
    public static function getMergedLpjRelativePath(Pengajuan $pengajuan, bool $forceRegenerate = false): string
    {
        $cacheDir = Storage::disk('local')->path('lpj-legalisir');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $sigHash = md5($pengajuan->tandaTanganDigitals->pluck('token_verifikasi')->join('|') . '|' . $pengajuan->state_id);
        $cacheFileName = "lpj-legalisir-{$pengajuan->id}-{$sigHash}.pdf";
        $cacheFilePath = $cacheDir . DIRECTORY_SEPARATOR . $cacheFileName;
        $relativeStoragePath = "lpj-legalisir/{$cacheFileName}";

        if (!$forceRegenerate && file_exists($cacheFilePath) && filesize($cacheFilePath) > 0) {
            return $relativeStoragePath;
        }

        // 1. Prioritas Utama: Jika pengajuan ini memiliki template LPJ Generator (Letter)
        // Kita render dokumen LPJ langsung dari template aslinya ('generator.lpj.pdf').
        // Tanda tangan digital (Ormawa, BKHM, WR3) otomatis tertanam LANGSUNG di tabel tanda tangan di bawah bagian K. Penutup!
        // Tanpa ada sisipan lembar cover/pengesahan tambahan yang tumpang tindih.
        $letter = self::resolveLpjLetter($pengajuan);
        if ($letter) {
            try {
                $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');
                $pdf = Pdf::loadView('generator.lpj.pdf', [
                    'lpj' => $letter,
                    'pengajuan' => $pengajuan,
                    'proposal' => $pengajuan,
                    'konfig' => $konfig,
                ])->setPaper('a4', 'portrait');

                file_put_contents($cacheFilePath, $pdf->output());
                return $relativeStoragePath;
            } catch (\Throwable $e) {
                Log::warning("Gagal me-render LPJ native untuk Pengajuan ID {$pengajuan->id}: " . $e->getMessage());
            }
        }

        // 2. Jika bukan dokumen LPJ Generator (berkas eksternal bebas yang diupload mahasiswa):
        // Kita satukan dokumen asli dengan menempatkan Lembar Pengesahan di HALAMAN TERAKHIR (Penutup / Sertifikat Pengesahan),
        // BUKAN di halaman pertama atau kedua yang merusak struktur cover/laporan mahasiswa.
        $origPath = self::resolveOriginalFilePath($pengajuan->file_lpj);

        // Jika berkas fisik asli tidak ditemukan atau bukan file PDF valid, hasilkan Lembar Pengesahan saja
        if (!$origPath || !file_exists($origPath) || !self::isValidPdf($origPath)) {
            $coverBinary = self::generateCoverPdf($pengajuan);
            file_put_contents($cacheFilePath, $coverBinary);
            return $relativeStoragePath;
        }

        // Buat Endorsement PDF di temporary file
        $tempEndorsementPath = tempnam(sys_get_temp_dir(), 'endorsement_lpj_') . '.pdf';
        file_put_contents($tempEndorsementPath, self::generateCoverPdf($pengajuan));

        try {
            $pdf = new Fpdi();
            $origPages = 0;
            try {
                $origPages = $pdf->setSourceFile($origPath);
            } catch (\Throwable $importException) {
                Log::warning("Gagal membaca berkas asli LPJ ID {$pengajuan->id} dengan FPDI: " . $importException->getMessage());
            }

            // 1. Import SELURUH halaman dokumen asli terlebih dahulu (Halaman 1 .. N)
            for ($pageNo = 1; $pageNo <= $origPages; $pageNo++) {
                try {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengimpor halaman {$pageNo} LPJ: " . $e->getMessage());
                }
            }

            // 2. Tambahkan Lembar Pengesahan & Legalisir di Halaman Terakhir (Page N+1)
            $endorsementPages = $pdf->setSourceFile($tempEndorsementPath);
            for ($pageNo = 1; $pageNo <= $endorsementPages; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            $pdf->Output($cacheFilePath, 'F');
        } catch (\Throwable $e) {
            Log::error("Kesalahan penggabungan PDF LPJ ID {$pengajuan->id}: " . $e->getMessage());
            file_put_contents($cacheFilePath, file_get_contents($tempEndorsementPath));
        } finally {
            if (file_exists($tempEndorsementPath)) {
                @unlink($tempEndorsementPath);
            }
        }

        return $relativeStoragePath;
    }

    public static function getMergedLpjPath(Pengajuan $pengajuan, bool $forceRegenerate = false): string
    {
        $relative = self::getMergedLpjRelativePath($pengajuan, $forceRegenerate);
        return Storage::disk('local')->path($relative);
    }

    /**
     * Membersihkan berkas cache gabungan LPJ ketika terjadi perubahan tanda tangan atau status.
     */
    public static function clearCache(Pengajuan $pengajuan): void
    {
        $cacheDir = Storage::disk('local')->path('lpj-legalisir');
        if (!is_dir($cacheDir)) {
            return;
        }

        $files = glob($cacheDir . DIRECTORY_SEPARATOR . "lpj-legalisir-{$pengajuan->id}-*.pdf");
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    /**
     * Mencari lokasi file absolut dari path storage (local disk atau public disk).
     */
    public static function resolveOriginalFilePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (file_exists($path)) {
            return $path;
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        if (file_exists(storage_path('app/' . $path))) {
            return storage_path('app/' . $path);
        }

        if (file_exists(public_path('storage/' . $path))) {
            return public_path('storage/' . $path);
        }

        return null;
    }

    /**
     * Memeriksa apakah suatu berkas adalah dokumen PDF yang valid secara struktur.
     */
    public static function isValidPdf(string $path): bool
    {
        if (!file_exists($path) || filesize($path) < 10) {
            return false;
        }

        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 5);
        if ($header !== '%PDF-') {
            fclose($handle);
            return false;
        }

        fseek($handle, max(0, filesize($path) - 1024));
        $tail = fread($handle, 1024);
        fclose($handle);

        return str_contains($tail, '%%EOF');
    }
}
