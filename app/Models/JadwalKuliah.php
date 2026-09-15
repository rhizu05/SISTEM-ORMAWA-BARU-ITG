<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalKuliah extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kuliahs';

    protected $fillable = [
        'ruangan_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'mata_kuliah',
        'semester',
        'aktif',
    ];

    protected $casts = [
        'hari' => 'integer',
        'aktif' => 'boolean',
    ];

    public const HARI = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(MasterRuangan::class, 'ruangan_id');
    }

    /**
     * Cek bentrok jadwal kuliah untuk ruangan & rentang waktu tertentu.
     * Jadwal kuliah berpola mingguan, jadi dicocokkan berdasarkan hari.
     */
    public static function bentrok(int $ruanganId, string $tanggalMulai, string $tanggalSelesai, string $jamMulai, string $jamSelesai): bool
    {
        $start = \Carbon\Carbon::parse($tanggalMulai);
        $end = \Carbon\Carbon::parse($tanggalSelesai);

        // Kumpulkan nomor hari (ISO: 1=Senin..7=Minggu) pada rentang tanggal.
        $hari = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $hari[$d->isoWeekday()] = true;
        }

        return static::where('ruangan_id', $ruanganId)
            ->where('aktif', true)
            ->whereIn('hari', array_keys($hari))
            ->where('jam_mulai', '<', $jamSelesai)
            ->where('jam_selesai', '>', $jamMulai)
            ->exists();
    }
}
