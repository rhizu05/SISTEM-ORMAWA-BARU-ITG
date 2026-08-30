<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'user_id',
        'judul',
        'isi',
        'file_lampiran'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
