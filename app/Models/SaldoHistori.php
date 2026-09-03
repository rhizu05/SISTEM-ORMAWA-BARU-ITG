<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaldoHistori extends Model
{
    protected $table = 'saldo_histori';

    protected $fillable = [
        'user_id',
        'actor_id',
        'tipe',
        'nominal_sebelum',
        'nominal_sesudah',
        'selisih',
        'catatan',
    ];

    protected $casts = [
        'nominal_sebelum' => 'decimal:2',
        'nominal_sesudah' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
