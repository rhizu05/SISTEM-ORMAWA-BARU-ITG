<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Regulasi extends Model
{
    use HasFactory;

    protected $table = 'regulasi';

    protected $fillable = [
        'user_id',
        'judul',
        'kategori',
        'deskripsi',
        'file_path'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
