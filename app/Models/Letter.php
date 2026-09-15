<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    protected $fillable = [
        'user_id',
        'proposal_otomatis_id',
        'type',
        'nomor_surat',
        'perihal',
        'content',
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
}
