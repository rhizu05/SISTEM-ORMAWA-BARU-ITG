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
        'kategori',
        'status',
        'disetujui_oleh_id',
        'catatan_kurasi',
        'tanggal_kegiatan',
        'file_lampiran',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePendingKurasi($query)
    {
        return $query->where('status', 'pending_kurasi');
    }

    public function getBadgeLabelAttribute(): string
    {
        if ($this->user && $this->user->hasRole('bkhm')) {
            return 'Resmi BKHM';
        }
        if ($this->user && $this->user->hasRole('bem')) {
            return 'BEM ITG';
        }
        if ($this->user && $this->user->hasRole('ormawa')) {
            return $this->user->name;
        }
        return 'Informasi Kampus';
    }

    public function getBadgeColorAttribute(): string
    {
        if ($this->user && $this->user->hasRole('bkhm')) {
            return 'bg-blue-100 text-blue-800 border-blue-200';
        }
        if ($this->user && $this->user->hasRole('bem')) {
            return 'bg-indigo-100 text-indigo-800 border-indigo-200';
        }
        return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'published' => 'Telah Terbit',
            'pending_kurasi' => 'Menunggu Kurasi BEM',
            'ditolak' => 'Ditolak BEM',
            default => ucfirst($this->status),
        };
    }
}
