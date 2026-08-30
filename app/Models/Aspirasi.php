<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    use HasFactory;
    
    protected $table = 'aspirasi';

    protected $fillable = [
        'nama_pengirim',
        'email_pengirim',
        'isi_aspirasi',
        'status',
        'tanggapan'
    ];
}
