<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = ['user_id','type','nomor_surat','perihal','content','metadata'];
    protected $casts = ['metadata'=>'array'];
}
