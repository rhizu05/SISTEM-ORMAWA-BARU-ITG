<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aspirasi extends Model
{
    use HasFactory;

    protected $table = 'aspirasis';

    protected $fillable = [
        'user_id',
        'judul',
        'isi',
        'kategori',
        'anonim',
        'status',
        'catatan_bpm'
    ];

    protected $casts = [
        'anonim' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
