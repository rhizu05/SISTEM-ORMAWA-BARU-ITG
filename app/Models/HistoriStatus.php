<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriStatus extends Model
{
    protected $table = 'histori_status';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'workflow_state_id',
        'catatan',
        'catatan_kendala',
        'unique_code'
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(WorkflowState::class, 'workflow_state_id');
    }
}
