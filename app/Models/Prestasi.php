<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'prestasis';

    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'penyelenggara',
        'tingkat',
        'juara',
        'tanggal',
        'afiliasi',
        'unit_terkait',
        'deskripsi',
        'file_bukti',
        'status',
        'catatan_bkhm',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const TINGKAT = ['Fakultas', 'Universitas', 'Regional', 'Nasional', 'Internasional'];
    public const AFILIASI = ['individu', 'ormawa', 'bem', 'ukm', 'lainnya'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTerverifikasi($query)
    {
        return $query->where('status', 'terverifikasi');
    }
}
