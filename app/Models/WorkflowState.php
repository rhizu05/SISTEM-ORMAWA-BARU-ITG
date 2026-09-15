<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowState extends Model
{
    public const DRAFT = 'draft';
    public const SUBMITTED = 'submitted';
    public const BEM_APPROVED = 'bem_approved';
    public const BPM_APPROVED = 'bpm_approved';
    public const BKHM_APPROVED = 'bkhm_approved';
    public const WR3_APPROVED = 'wr3_approved';
    public const TO_TREASURER = 'to_treasurer';
    public const FUNDS_DISBURSED = 'funds_disbursed';
    public const LPJ_SUBMITTED = 'lpj_submitted';
    public const LPJ_WR3_REVIEW = 'lpj_wr3_review';
    public const COMPLETED = 'completed';
    public const REJECTED = 'rejected';

    protected $fillable = ['name', 'label', 'order_num', 'pic_role', 'pic_contact'];

    public function transitions()
    {
        return $this->hasMany(WorkflowTransition::class, 'from_state_id');
    }
}
