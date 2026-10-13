<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProposalOtomatis extends Model
{
    use HasFactory;

    protected $table = 'proposal_otomatis';

    protected $fillable = [
        'user_id', 'nama_kegiatan', 'latar_belakang', 'tujuan', 'sasaran',
        'indikator', 'luaran', 'dampak', 'penutup',
        'ttd_1_role', 'ttd_1_nama', 'ttd_1_jabatan', 'ttd_1_nim', 'ttd_1_file',
        'ttd_2_role', 'ttd_2_nama', 'ttd_2_jabatan', 'ttd_2_nim', 'ttd_2_file',
        'ttd_3_role', 'ttd_3_nama', 'ttd_3_jabatan', 'ttd_3_nim', 'ttd_3_file',
        'penandatangan',
        'status'
    ];

    protected $casts = [
        'penandatangan' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tandaTanganDigitals(): MorphMany
    {
        return $this->morphMany(TandaTanganDigital::class, 'signable')->orderBy('signer_index');
    }

    /**
     * Daftar penandatangan yang sudah dinormalkan, dengan fallback ke kolom
     * ttd_1..ttd_3 lama bila data baru belum ada.
     */
    public function getPenandatanganListAttribute(): array
    {
        $list = $this->penandatangan ?? [];

        if (empty($list)) {
            foreach ([1, 2, 3] as $n) {
                $nama = $this->{"ttd_{$n}_nama"};
                if (! empty($nama)) {
                    $list[] = [
                        'jenis' => 'internal',
                        'role' => $this->{"ttd_{$n}_role"},
                        'nama' => $nama,
                        'jabatan' => $this->{"ttd_{$n}_jabatan"},
                        'nim' => $this->{"ttd_{$n}_nim"},
                    ];
                }
            }
        }

        return array_map(function ($p) {
            $role = $p['role'] ?? null;
            return [
                'jenis' => $p['jenis'] ?? 'internal',
                'role' => $role,
                'nama' => $p['nama'] ?? '-',
                'jabatan' => $p['jabatan'] ?? ($role ? ucfirst(str_replace('_', ' ', $role)) : 'Penandatangan'),
                'nim' => $p['nim'] ?? null,
            ];
        }, $list);
    }

    public function getSignaturesByIndexAttribute(): array
    {
        return $this->tandaTanganDigitals->keyBy('signer_index')->all();
    }

    public function rab()
    {
        return $this->hasMany(ProposalRab::class, 'proposal_id');
    }

    public function panitia()
    {
        return $this->hasMany(ProposalPanitia::class, 'proposal_id');
    }
}
