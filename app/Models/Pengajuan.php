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

    public function dana(): HasOne
    {
        return $this->hasOne(Dana::class);
    }
}
