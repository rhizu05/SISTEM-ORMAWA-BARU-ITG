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
        'status',
        'catatan_bpm'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
