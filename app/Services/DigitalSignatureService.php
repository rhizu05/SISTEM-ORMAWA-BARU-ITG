<?php

namespace App\Services;

use App\Models\Konfigurasi;
use App\Models\Letter;
use App\Models\Pengajuan;
use App\Models\PeminjamanBarang;
use App\Models\PeminjamanTempat;
use App\Models\ProposalOtomatis;
use App\Models\SuratPeringatan;
use App\Models\TandaTanganDigital;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalSignatureService
{
    /**
     * Membubuhkan tanda tangan digital kriptografis pada model dokumen.
     */
    public static function sign(Model $model, ?User $user, string $role, array $customPejabat = [], int $signerIndex = 0): TandaTanganDigital
    {
        $konfig = Konfigurasi::pluck('nilai_konfigurasi', 'nama_konfigurasi');

        // 1. Tentukan identitas snapshot penandatangan
        $nama = $customPejabat['nama'] ?? null;
        $nidn = $customPejabat['nidn'] ?? null;
        $jabatan = $customPejabat['jabatan'] ?? null;

        if (! $nama) {
            if ($role === 'wr3' || $role === 'wr3_lpj') {
                $nama = ($konfig['wr3_nama'] ?? null) ?: 'Pejabat Wakil Rektor III';
                $nidn = ($konfig['wr3_nidn'] ?? null) ?: '-';
                $jabatan = $konfig['wr3_jabatan'] ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama';
            } elseif ($role === 'bkhm' || $role === 'bkhm_lpj') {
                $nama = ($konfig['bkhm_nama'] ?? null) ?: 'Pejabat Kepala BKHM';
                $nidn = ($konfig['bkhm_nidn'] ?? null) ?: '-';
                $jabatan = $konfig['bkhm_jabatan'] ?? 'Kepala Biro Kemahasiswaan dan Hubungan Masyarakat (BKHM)';
            } elseif ($role === 'sarpras') {
                $nama = $user?->name ?? 'Bagian Sarana dan Prasarana ITG';
                $nidn = null;
                $jabatan = 'Penanggung Jawab Sarana & Prasarana ITG';
            } elseif ($role === 'bendahara') {
                $nama = ($konfig['bendahara_nama'] ?? null) ?: ($user?->name ?? 'Bendahara Kampus ITG');
                $nidn = ($konfig['bendahara_nidn'] ?? null) ?: '-';
                $jabatan = $konfig['bendahara_jabatan'] ?? 'Bendahara Pengeluaran ITG';
            } elseif ($role === 'bpm') {
                $nama = ($user?->nama_ketua ?? null) ?: ($user?->name ?? 'Ketua BPM ITG');
                $nidn = ($user?->nim_ketua ?? null) ?: '-';
                $jabatan = 'Ketua Badan Perwakilan Mahasiswa (BPM) ITG';
            } elseif ($role === 'bem') {
                $nama = ($user?->nama_ketua ?? null) ?: ($user?->name ?? 'Presiden Mahasiswa ITG');
                $nidn = ($user?->nim_ketua ?? null) ?: '-';
                $jabatan = 'Presiden Mahasiswa BEM ITG';
            } elseif ($role === 'ormawa' || $role === 'ormawa_lpj') {
                $nama = ($user?->nama_ketua ?? null) ?: ($user?->name ?? 'Ketua Ormawa');
                $nidn = ($user?->nim_ketua ?? null) ?: '-';
                $jabatan = 'Ketua ' . ($user?->name ?? 'Ormawa');
            } else {
                $nama = $user?->name ?: 'Pejabat Berwenang';
                $nidn = $user?->nim_ketua ?? null;
                $jabatan = strtoupper($role) . ' ITG';
            }
        }

        // 2. Buat snapshot data penting dokumen (Payload Kanonikal)
        $snapshot = self::buildPayloadSnapshot($model, $role, $nama, $signerIndex, $jabatan);
        $nomorSurat = $snapshot['nomor_surat'] ?? null;

        // 3. Kalkulasi sidik jari kriptografis HMAC-SHA256 dengan kanonikalisasi deterministik
        $canonicalString = self::canonicalizePayload($snapshot);
        $appKey = config('app.key') ?: 'ITG-SKIN-DIGITAL-SIGNATURE-SECRET-KEY';
        $signatureHash = hash_hmac('sha256', $canonicalString, $appKey);

        // 4. Token validasi unik publik format: SKIN-SIG-YYYY-XXXXXXXXXXXX
        $token = 'SKIN-SIG-' . date('Y') . '-' . strtoupper(Str::random(12));

        // 5. Simpan record tanda tangan digital
        return TandaTanganDigital::updateOrCreate(
            [
                'signable_type' => get_class($model),
                'signable_id'   => $model->id,
                'role'          => $role,
                'signer_index'  => $signerIndex,
            ],
            [
                'user_id'              => $user?->id,
                'nama_penandatangan'   => $nama,
                'jabatan_penandatangan'=> $jabatan,
                'nidn_penandatangan'   => $nidn,
                'nomor_surat'          => $nomorSurat,
                'token_verifikasi'     => $token,
                'signature_hash'       => $signatureHash,
                'payload_snapshot'     => $snapshot,
                'signed_at'            => now(),
                'ip_address'           => request()?->ip(),
                'user_agent'           => request()?->userAgent() ? substr(request()->userAgent(), 0, 500) : null,
                'is_valid'             => true,
            ]
        );
    }

    /**
     * Bubuhkan tanda tangan untuk setiap penandatangan internal dalam satu daftar.
     * Entri berjenis 'eksternal' dilewati karena ditandatangani manual di luar sistem.
     * Mengembalikan peta tanda tangan berdasarkan signer_index (posisi di daftar).
     */
    public static function signMany(Model $model, array $signers, ?User $actor): array
    {
        $signed = [];
        $keep = [];

        foreach ($signers as $index => $signer) {
            if (($signer['jenis'] ?? 'internal') === 'eksternal') {
                continue;
            }

            $signed[$index] = self::sign(
                $model,
                $actor,
                $signer['role'] ?? 'pejabat',
                [
                    'nama' => $signer['nama'] ?? null,
                    'jabatan' => $signer['jabatan'] ?? null,
                    'nidn' => $signer['nidn'] ?? ($signer['nim'] ?? null),
                ],
                $index
            );
            $keep[] = $index;
        }

        self::pruneSignatures($model, $keep);

        return $signed;
    }

    /**
     * Hapus tanda tangan yang posisinya tidak lagi dipakai, mis. ketika daftar
     * penandatangan diperpendek saat dokumen disimpan ulang.
     */
    public static function pruneSignatures(Model $model, array $keepIndices): void
    {
        $query = TandaTanganDigital::where('signable_type', get_class($model))
            ->where('signable_id', $model->id);

        if (empty($keepIndices)) {
            $query->delete();
            return;
        }

        $query->whereNotIn('signer_index', $keepIndices)->delete();
    }

    /**
     * Membangun payload ringkasan dokumen untuk hashing, dilengkapi identitas
     * penandatangan (jabatan + posisi) agar tiap tanda tangan punya hash berbeda.
     */
    protected static function buildPayloadSnapshot(Model $model, string $role, string $signerName, int $signerIndex = 0, ?string $jabatan = null): array
    {
        $snapshot = self::buildDocumentSnapshot($model, $role, $signerName);
        $snapshot['jabatan'] = $jabatan ?? $role;
        $snapshot['signer_index'] = $signerIndex;

        return $snapshot;
    }

    /**
     * Ringkasan isi dokumen per jenis model.
     */
    protected static function buildDocumentSnapshot(Model $model, string $role, string $signerName): array
    {
        if ($model instanceof SuratPeringatan) {
            $penerimaList = $model->isMahasiswa()
                ? array_map(fn ($m) => [
                    'nim' => $m['nim'],
                    'nama' => $m['nama'],
                    'prodi' => $m['prodi'],
                ], $model->penerima_mahasiswa)
                : [];

            return [
                'document_type'   => 'Surat Peringatan (' . $model->tingkat . ')',
                'nomor_surat'     => $model->nomor_surat,
                'perihal'         => $model->perihal,
                'alasan'          => $model->alasan_singkat,
                'tipe_sasaran'    => $model->tipe_sasaran,
                'penerima'        => $model->nama_penerima,
                'identitas'       => $model->identitas_penerima,
                'penerima_list'   => $penerimaList,
                'tanggal_surat'   => $model->tanggal_surat?->format('Y-m-d') ?: date('Y-m-d'),
                'penandatangan'   => $signerName,
                'role'            => $role,
            ];
        }

        if ($model instanceof Pengajuan) {
            $isLpj = in_array($role, ['ormawa_lpj', 'bkhm_lpj', 'wr3_lpj'])
                || (!empty($model->file_lpj) && (in_array($model->state?->name, ['lpj_submitted', 'lpj_wr3_review', 'completed']) || str_contains($model->state?->name ?? '', 'lpj')));
            $docType = $isLpj ? 'Pengesahan Laporan Pertanggungjawaban (LPJ)' : 'Pengesahan Proposal Kegiatan';

            $tahapLabel = match ($role) {
                'ormawa_lpj' => 'Pelapor / Pengunggah LPJ (Ormawa)',
                'bkhm_lpj'   => 'Verifikasi & Legalisir LPJ (BKHM)',
                'wr3_lpj'    => 'Pengesahan Akhir LPJ (Wakil Rektor III)',
                default      => strtoupper($role),
            };

            return [
                'document_type'   => $docType,
                'nomor_surat'     => $model->nomor_surat ?? ('PROP/' . date('Y') . '/' . str_pad($model->id, 4, '0', STR_PAD_LEFT)),
                'nama_kegiatan'   => $model->nama_kegiatan,
                'ormawa'          => $model->user?->name ?? 'Ormawa ITG',
                'dana_diajukan'   => (float) $model->dana_diajukan,
                'tanggal_mulai'   => $model->tanggal_mulai_kegiatan ? $model->tanggal_mulai_kegiatan->format('Y-m-d') : null,
                'tanggal_selesai' => $model->tanggal_selesai_kegiatan ? $model->tanggal_selesai_kegiatan->format('Y-m-d') : null,
                'tahap'           => $tahapLabel,
                'penandatangan'   => $signerName,
            ];
        }

        if ($model instanceof PeminjamanTempat) {
            return [
                'document_type'   => 'Surat Izin Peminjaman Ruangan & Fasilitas Kampus',
                'nomor_surat'     => 'IZN-TMP/' . date('Y') . '/' . str_pad($model->id, 4, '0', STR_PAD_LEFT),
                'pemohon'         => $model->user?->name ?? 'Pemohon',
                'nama_kegiatan'   => $model->nama_kegiatan,
                'ruangan'         => $model->ruangan?->nama_ruangan ?? 'Ruangan ITG',
                'tanggal_mulai'   => $model->tgl_mulai,
                'tanggal_selesai' => $model->tgl_selesai,
                'tahap'           => strtoupper($role),
                'penandatangan'   => $signerName,
            ];
        }

        if ($model instanceof PeminjamanBarang) {
            $totalItems = is_array($model->kebutuhan_barang) ? count($model->kebutuhan_barang) : 0;
            return [
                'document_type'   => 'Surat Bukti Peminjaman Barang Inventaris Kampus',
                'nomor_surat'     => 'IZN-BRG/' . date('Y') . '/' . str_pad($model->id, 4, '0', STR_PAD_LEFT),
                'pemohon'         => $model->user?->name ?? 'Pemohon',
                'nama_kegiatan'   => $model->nama_kegiatan,
                'total_item'      => $totalItems . ' item perlengkapan',
                'tanggal_mulai'   => $model->tgl_mulai,
                'tanggal_selesai' => $model->tgl_selesai,
                'tahap'           => strtoupper($role),
                'penandatangan'   => $signerName,
            ];
        }

        if ($model instanceof Letter) {
            $jenisLabel = match($model->type) {
                'undangan'        => 'Surat Undangan',
                'tugas'           => 'Surat Tugas / Mandat',
                'permohonan'      => 'Surat Permohonan',
                'keterangan_aktif'=> 'Surat Keterangan Aktif',
                'lpj'             => 'Laporan Pertanggungjawaban (LPJ)',
                default           => 'Surat Resmi Ormawa',
            };
            return [
                'document_type' => $jenisLabel,
                'nomor_surat'   => $model->nomor_surat ?? '-',
                'perihal'       => $model->perihal,
                'jenis_surat'   => $model->type,
                'penerbit'      => $model->user?->name ?? 'Ormawa ITG',
                'tujuan'        => $model->metadata['tujuan'] ?? '-',
                'tanggal'       => now()->format('Y-m-d'),
                'penandatangan' => $signerName,
                'role'          => $role,
            ];
        }

        if ($model instanceof ProposalOtomatis) {
            return [
                'document_type'   => 'Proposal Kegiatan Otomatis',
                'nomor_surat'     => 'PROP/' . date('Y') . '/' . str_pad($model->id, 4, '0', STR_PAD_LEFT),
                'nama_kegiatan'   => $model->nama_kegiatan,
                'ormawa'          => $model->user?->name ?? 'Ormawa ITG',
                'tanggal'         => now()->format('Y-m-d'),
                'penandatangan'   => $signerName,
                'role'            => $role,
            ];
        }

        return [
            'document_type' => class_basename($model),
            'id'            => $model->id,
            'role'          => $role,
            'penandatangan' => $signerName,
            'timestamp'     => now()->toIso8601String(),
        ];
    }

    /**
     * Menghasilkan string QR Code dalam format SVG murni.
     */
    public static function generateQrCodeSvg(string $content, int $size = 140): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString($content);
    }

    /**
     * Menghasilkan Data URI Base64 SVG untuk disematkan langsung di <img src="..."> HTML/PDF.
     */
    public static function generateQrCodeDataUri(string $content, int $size = 140): string
    {
        $svg = self::generateQrCodeSvg($content, $size);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Memverifikasi keabsahan tanda tangan digital melalui token publik.
     */
    public static function verify(string $token): ?array
    {
        $signature = TandaTanganDigital::with(['signable', 'user'])
            ->where('token_verifikasi', $token)
            ->first();

        if (! $signature || ! $signature->is_valid) {
            return null;
        }

        // Hitung ulang hash kriptografis untuk membuktikan integritas dengan kanonikalisasi deterministik
        $canonicalString = self::canonicalizePayload((array) $signature->payload_snapshot);
        $appKey = config('app.key') ?: 'ITG-SKIN-DIGITAL-SIGNATURE-SECRET-KEY';
        $calculatedHash = hash_hmac('sha256', $canonicalString, $appKey);

        $isAuthentic = hash_equals($signature->signature_hash, $calculatedHash);

        return [
            'is_authentic'    => $isAuthentic,
            'signature'       => $signature,
            'token'           => $signature->token_verifikasi,
            'role'            => $signature->role,
            'pejabat_nama'    => $signature->nama_penandatangan,
            'pejabat_jabatan' => $signature->jabatan_penandatangan,
            'pejabat_nidn'    => $signature->nidn_penandatangan,
            'identitas_label' => $signature->identitas_label,
            'identitas_formatted' => $signature->identitas_formatted,
            'nomor_surat'     => $signature->nomor_surat,
            'signed_at'       => $signature->signed_at,
            'formatted_date'  => $signature->formatted_signed_at,
            'snapshot'        => $signature->payload_snapshot,
            'hash'            => $signature->signature_hash,
            'model'           => $signature->signable,
        ];
    }

    /**
     * Menghasilkan representasi string JSON yang deterministik dan kanonikal.
     * Mengurutkan kunci array secara alfabetis (ksort rekursif) agar invariant terhadap
     * normalisasi atau reordering penyimpanan kolom JSON MySQL.
     */
    public static function canonicalizePayload(array $data): string
    {
        return json_encode(self::sortKeysRecursively($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function sortKeysRecursively(array $data): array
    {
        ksort($data);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::sortKeysRecursively($value);
            }
        }
        return $data;
    }
}
