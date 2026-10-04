<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TandaTanganDigital extends Model
{
    use HasFactory;

    protected $table = 'tanda_tangan_digitals';

    protected $fillable = [
        'signable_type',
        'signable_id',
        'user_id',
        'role',
        'signer_index',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'nidn_penandatangan',
        'nomor_surat',
        'token_verifikasi',
        'signature_hash',
        'payload_snapshot',
        'signed_at',
        'ip_address',
        'user_agent',
        'is_valid',
    ];

    protected $casts = [
        'payload_snapshot' => 'array',
        'signed_at' => 'datetime',
        'is_valid' => 'boolean',
        'signer_index' => 'integer',
    ];

    public function signable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('dokumen.verifikasi', ['token' => $this->token_verifikasi]);
    }

    public function getFormattedSignedAtAttribute(): string
    {
        return $this->signed_at
            ? $this->signed_at->translatedFormat('l, d F Y - H:i:s') . ' WIB'
            : '-';
    }

    /**
     * Mengembalikan label identitas nomor resmi (NIDN untuk dosen, NIM untuk mahasiswa).
     */
    public function getIdentitasLabelAttribute(): string
    {
        $role = strtolower($this->role ?? '');
        $jabatan = strtolower($this->jabatan_penandatangan ?? '');

        // Kategori Dosen: WR3, BKHM, Bendahara
        if (in_array($role, ['wr3', 'bkhm', 'bendahara', 'wr3_lpj', 'bkhm_lpj']) || str_contains($jabatan, 'rektor') || str_contains($jabatan, 'bkhm') || str_contains($jabatan, 'bendahara kampus')) {
            return 'NIDN';
        }

        // Kategori Mahasiswa: BEM, BPM, HIMA, UKM, Ormawa
        if (in_array($role, ['bem', 'bpm', 'ormawa', 'hima', 'ukm', 'mahasiswa', 'ormawa_lpj']) || str_contains($jabatan, 'mahasiswa') || str_contains($jabatan, 'ketua umum') || str_contains($jabatan, 'presiden')) {
            return 'NIM';
        }

        return 'NIDN';
    }

    /**
     * Label representasi peran penandatangan yang ramah pengguna.
     */
    public function getRoleBadgeLabelAttribute(): string
    {
        return match (strtolower($this->role ?? '')) {
            'ormawa_lpj' => 'Pelapor LPJ (Ormawa)',
            'bkhm_lpj'   => 'Verifikasi LPJ (BKHM)',
            'wr3_lpj'    => 'Pengesahan LPJ (WR3)',
            'wr3'        => 'Wakil Rektor III',
            'bkhm'       => 'Kepala BKHM',
            'bpm'        => 'BPM ITG',
            'bem'        => 'BEM ITG',
            'bendahara'  => 'Bendahara Kampus',
            'sarpras'    => 'Sarpras ITG',
            'ormawa'     => 'Ormawa Pengusul',
            default      => strtoupper($this->role ?? 'Pejabat'),
        };
    }

    /**
     * Format string identitas penandatangan: NIDN. <nomor> atau NIM. <nomor>.
     */
    public function getIdentitasFormattedAttribute(): ?string
    {
        if (empty($this->nidn_penandatangan) || $this->nidn_penandatangan === '-') {
            return null;
        }

        return $this->identitas_label . '. ' . $this->nidn_penandatangan;
    }
}
