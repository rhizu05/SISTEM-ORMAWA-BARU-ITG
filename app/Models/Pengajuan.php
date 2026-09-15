<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'dana_diajukan',
        'tanggal_pengajuan',
        'file_proposal',
        'file_lpj',
        'tanggal_upload_lpj',
        'workflow_state_id',
        'rejected_from_state_id',
        'catatan_revisi',
        'nomor_surat',
        'unique_code',
        'notif_cair_terlihat'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(WorkflowState::class, 'workflow_state_id');
    }

    public function histori(): HasMany
    {
        return $this->hasMany(HistoriStatus::class);
    }

    /**
     * FR-011: pesan komunikasi/follow-up pengajuan.
     */
    public function komunikasi(): HasMany
    {
        return $this->hasMany(KomunikasiPengajuan::class)->orderBy('created_at');
    }

    public function dana(): HasOne
    {
        return $this->hasOne(Dana::class)->latestOfMany();
    }

    /**
     * Seluruh termin pencairan dana untuk pengajuan ini.
     */
    public function danaList(): HasMany
    {
        return $this->hasMany(Dana::class)->orderBy('termin_ke');
    }

    /**
     * Total dana yang telah dicairkan (semua termin).
     */
    public function totalDicairkan(): float
    {
        return (float) $this->danaList()->sum('nominal_cair');
    }

    /**
     * Nomor termin berikutnya (1 jika belum pernah dicairkan).
     */
    public function terminBerikutnya(): int
    {
        return ((int) $this->danaList()->max('termin_ke')) + 1;
    }
}
