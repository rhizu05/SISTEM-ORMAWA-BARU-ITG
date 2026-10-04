<?php

use App\Models\WorkflowState;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        WorkflowState::firstOrCreate(
            ['name' => 'cancelled'],
            [
                'label' => 'Dibatalkan Pengaju',
                'order_num' => 98,
                'pic_role' => 'Ormawa Pengaju',
                'pic_contact' => 'Dibatalkan oleh Pengaju',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WorkflowState::where('name', 'cancelled')->delete();
    }
};
