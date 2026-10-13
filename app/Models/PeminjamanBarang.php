<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'file_persetujuan_prodi',
        'status_bkhm',
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

    public function tandaTanganDigitals(): MorphMany
    {
        return $this->morphMany(TandaTanganDigital::class, 'signable')->latest();
    }

    public function suratPermohonan(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Letter::class, 'peminjaman');
    }
}
