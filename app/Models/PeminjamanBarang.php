<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanBarang extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_barang';

    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'tgl_mulai',
        'tgl_selesai',
        'kebutuhan_barang',
        'status_bkkh',
        'status_sarpras',
        'status_akhir',
        'catatan_penolakan'
    ];

    protected $casts = [
        'kebutuhan_barang' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
