<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_states', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. draft, submitted_to_bem
            $table->string('label'); // e.g. Draft, Diajukan ke BEM
            $table->integer('order_num')->default(0);
            $table->timestamps();
        });

        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_state_id')->constrained('workflow_states')->onDelete('cascade');
            $table->foreignId('to_state_id')->constrained('workflow_states')->onDelete('cascade');
            $table->string('action_label'); // e.g. Setujui, Tolak
            $table->string('required_role')->nullable(); // role allowed to perform this transition
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
        Schema::dropIfExists('workflow_states');
    }
};
