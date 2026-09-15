<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dana extends Model
{
    protected $table = 'dana';

    protected $fillable = [
        'pengajuan_id',
        'termin_ke',
        'nominal_cair',
        'tanggal_cair',
        'catatan',
    ];

    protected $casts = [
        'termin_ke' => 'integer',
        'nominal_cair' => 'decimal:2',
        'tanggal_cair' => 'date',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
