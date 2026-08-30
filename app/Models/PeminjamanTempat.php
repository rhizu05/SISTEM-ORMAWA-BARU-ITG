<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanTempat extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_tempat';

    protected $fillable = [
        'user_id',
        'ruangan_id',
        'nama_kegiatan',
        'tgl_mulai',
        'tgl_selesai',
        'jam_mulai',
        'jam_selesai',
        'deskripsi_kegiatan',
        'status_bkkh',
        'status_sarpras',
        'status_akhir',
        'catatan_penolakan'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(MasterRuangan::class, 'ruangan_id');
    }
}
