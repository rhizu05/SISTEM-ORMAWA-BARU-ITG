<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalRapat extends Model
{
    use HasFactory;

    protected $table = 'jadwal_rapat';

    protected $fillable = [
        'user_id',
        'judul_rapat',
        'deskripsi',
        'tanggal_rapat',
        'jam_rapat',
        'lokasi',
        'link_meeting',
        'target_peserta'
    ];

    protected $casts = [
        'target_peserta' => 'array',
    ];

    public function penyelenggara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
