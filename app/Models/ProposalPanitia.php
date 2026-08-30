<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalPanitia extends Model
{
    use HasFactory;

    protected $table = 'proposal_panitia';

    protected $fillable = [
        'proposal_id', 'jabatan', 'nama_mahasiswa', 'nim'
    ];
}
