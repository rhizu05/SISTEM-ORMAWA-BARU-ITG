<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPeringatan extends Model
{
    protected $fillable = [
        'target_user_id','nomor_surat','tingkat','perihal','alasan_singkat','deskripsi','sanksi','tanggal_surat','penandatangan','pdf_path','created_by'
    ];

    protected $casts = ['tanggal_surat'=>'date'];

    public function target(): BelongsTo { return $this->belongsTo(User::class, 'target_user_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
