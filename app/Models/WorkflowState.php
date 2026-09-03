<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowState extends Model
{
    protected $fillable = ['name', 'label', 'order_num', 'pic_role', 'pic_contact'];

    public function transitions()
    {
        return $this->hasMany(WorkflowTransition::class, 'from_state_id');
    }
}
