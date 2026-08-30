<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalRab extends Model
{
    use HasFactory;

    protected $table = 'proposal_rab';

    protected $fillable = [
        'proposal_id', 'rincian', 'volume', 'satuan', 'harga_satuan', 'total_harga'
    ];
}
