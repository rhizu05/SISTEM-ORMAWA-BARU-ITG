<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_proker',
        'deskripsi',
        'rencana_pelaksanaan',
        'status',
        'catatan_bpm',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Proposal kegiatan yang terhubung dengan program kerja ini.
     */
    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'program_kerja_id');
    }
}
