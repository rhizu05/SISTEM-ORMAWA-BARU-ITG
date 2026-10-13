<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'file_persetujuan_prodi',
        'status_bkhm',
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

    public function tandaTanganDigitals(): MorphMany
    {
        return $this->morphMany(TandaTanganDigital::class, 'signable')->latest();
    }

    public function suratPermohonan(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Letter::class, 'peminjaman');
    }
}
