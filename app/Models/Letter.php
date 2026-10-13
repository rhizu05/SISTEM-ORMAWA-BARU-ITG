<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Letter extends Model
{
    protected $fillable = [
        'user_id',
        'proposal_otomatis_id',
        'peminjaman_id',
        'peminjaman_type',
        'type',
        'nomor_surat',
        'perihal',
        'lampiran',
        'content',
        'file_lampiran_pdf',
        'metadata',
    ];

    protected $casts = ['metadata' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(ProposalOtomatis::class, 'proposal_otomatis_id');
    }

    public function peminjaman(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function tandaTanganDigitals(): MorphMany
    {
        return $this->morphMany(TandaTanganDigital::class, 'signable')->orderBy('signer_index');
    }

    public function tandaTanganUtama(): ?TandaTanganDigital
    {
        return $this->tandaTanganDigitals()->first();
    }

    /**
     * Daftar penandatangan yang sudah dinormalkan. Entri lama {role, nama}
     * diperlakukan sebagai penandatangan internal agar surat lama tetap tampil.
     */
    public function getPenandatanganListAttribute(): array
    {
        $meta = $this->metadata ?? [];
        $list = $meta['penandatangan_list'] ?? [];
        $owner = $this->user ?? Auth::user();

        if (empty($list) && ! empty($meta['penandatangan'])) {
            $role = $meta['penandatangan'];
            $nama = match ($role) {
                'sekretaris' => $owner->nama_sekretaris ?? $owner->name ?? null,
                'bendahara' => $owner->nama_bendahara ?? $owner->name ?? null,
                default => $owner->nama_ketua ?? $owner->name ?? null,
            };
            $nim = match ($role) {
                'sekretaris' => $owner->nim_sekretaris ?? null,
                'bendahara' => $owner->nim_bendahara ?? null,
                default => $owner->nim_ketua ?? null,
            };
            $list = [['role' => $role, 'nama' => $nama, 'nim' => $nim]];
        }

        return array_map(function ($p) use ($owner) {
            $role = $p['role'] ?? null;
            $jenis = $p['jenis'] ?? 'internal';
            $nim = $p['nim'] ?? null;
            if ($jenis === 'internal' && empty($nim) && $role && $owner) {
                $nim = match ($role) {
                    'ketua' => $owner->nim_ketua ?? null,
                    'sekretaris' => $owner->nim_sekretaris ?? null,
                    'bendahara' => $owner->nim_bendahara ?? null,
                    default => null,
                };
            }

            return [
                'jenis' => $jenis,
                'role' => $role,
                'nama' => $p['nama'] ?? '-',
                'jabatan' => $p['jabatan'] ?? ($role ? ucfirst($role) : 'Penandatangan'),
                'nim' => $nim,
            ];
        }, $list);
    }

    /**
     * Peta tanda tangan berdasarkan signer_index, untuk dicocokkan dengan posisi penandatangan.
     */
    public function getSignaturesByIndexAttribute(): array
    {
        return $this->tandaTanganDigitals->keyBy('signer_index')->all();
    }
}
