<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalOtomatis extends Model
{
    use HasFactory;

    protected $table = 'proposal_otomatis';

    protected $fillable = [
        'user_id', 'nama_kegiatan', 'latar_belakang', 'tujuan', 'sasaran', 'penutup',
        'ttd_1_role', 'ttd_1_nama', 'ttd_1_jabatan', 'ttd_1_nim', 'ttd_1_file',
        'ttd_2_role', 'ttd_2_nama', 'ttd_2_jabatan', 'ttd_2_nim', 'ttd_2_file',
        'ttd_3_role', 'ttd_3_nama', 'ttd_3_jabatan', 'ttd_3_nim', 'ttd_3_file',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rab()
    {
        return $this->hasMany(ProposalRab::class, 'proposal_id');
    }

    public function panitia()
    {
        return $this->hasMany(ProposalPanitia::class, 'proposal_id');
    }
}
