<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowTransition extends Model
{
    protected $fillable = ['from_state_id', 'to_state_id', 'action_label', 'required_role'];

    public function fromState()
    {
        return $this->belongsTo(WorkflowState::class, 'from_state_id');
    }

    public function toState()
    {
        return $this->belongsTo(WorkflowState::class, 'to_state_id');
    }
}
