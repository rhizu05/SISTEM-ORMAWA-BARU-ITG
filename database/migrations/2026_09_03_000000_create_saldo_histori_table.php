<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo_histori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $table->string('tipe', 30);
            $table->decimal('nominal_sebelum', 15, 2);
            $table->decimal('nominal_sesudah', 15, 2);
            $table->decimal('selisih', 15, 2);
            $table->text('catatan');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['actor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_histori');
    }
};
