<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomunikasiPengajuan extends Model
{
    protected $table = 'komunikasi_pengajuans';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'pesan',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
