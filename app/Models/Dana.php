<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dana extends Model
{
    protected $table = 'dana';

    protected $fillable = [
        'pengajuan_id',
        'nominal_cair',
        'tanggal_cair'
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
