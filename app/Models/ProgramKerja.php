<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
