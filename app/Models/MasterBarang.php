<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBarang extends Model
{
    use HasFactory;

    protected $table = 'master_barang';

    protected $fillable = [
        'nama_barang',
        'stok_tersedia',
        'status_aktif',
        'boleh_dibawa_keluar',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'boleh_dibawa_keluar' => 'boolean',
    ];
}
