<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeAnggaran extends Model
{
    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'aktif' => 'boolean',
    ];

    /**
     * Periode anggaran yang sedang berjalan (dipakai saat pencatatan saldo).
     */
    public static function aktif(): ?self
    {
        return static::where('aktif', true)->latest('tanggal_mulai')->first();
    }

    public function saldoHistori(): HasMany
    {
        return $this->hasMany(SaldoHistori::class);
    }
}
