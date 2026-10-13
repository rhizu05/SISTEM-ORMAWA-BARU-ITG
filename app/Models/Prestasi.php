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
        'jenis_kepesertaan',
        'nama_tim',
        'dosen_pembimbing',
        'anggota_tim',
        'nama_kegiatan',
        'penyelenggara',
        'tingkat',
        'juara',
        'tanggal',
        'tanggal_mulai',
        'tanggal_selesai',
        'url_penyelenggara',
        'afiliasi',
        'unit_terkait',
        'deskripsi',
        'file_bukti',
        'foto_penyerahan',
        'status',
        'catatan_bkhm',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'anggota_tim' => 'array',
    ];

    public function isKelompok(): bool
    {
        return $this->jenis_kepesertaan === 'kelompok';
    }

    public function getDaftarAnggotaAttribute(): array
    {
        return is_array($this->anggota_tim) ? $this->anggota_tim : [];
    }

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

    public function getRentangTanggalAttribute(): string
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            if ($this->tanggal_mulai->format('Y-m-d') === $this->tanggal_selesai->format('Y-m-d')) {
                return $this->tanggal_mulai->translatedFormat('d F Y');
            }
            if ($this->tanggal_mulai->format('Y-m') === $this->tanggal_selesai->format('Y-m')) {
                return $this->tanggal_mulai->translatedFormat('d') . ' - ' . $this->tanggal_selesai->translatedFormat('d F Y');
            }
            return $this->tanggal_mulai->translatedFormat('d M Y') . ' - ' . $this->tanggal_selesai->translatedFormat('d M Y');
        }

        if ($this->tanggal_mulai) {
            return $this->tanggal_mulai->translatedFormat('d F Y');
        }

        if ($this->tanggal) {
            return $this->tanggal->translatedFormat('d F Y');
        }

        return $this->created_at ? $this->created_at->translatedFormat('d F Y') : '-';
    }
}
