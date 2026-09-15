<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Q-SAR-02: jadwal perkuliahan pola mingguan yang berulang selama satu semester.
        Schema::create('jadwal_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('master_ruangan')->onDelete('cascade');
            $table->unsignedTinyInteger('hari'); // 1=Senin ... 7=Minggu
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('mata_kuliah');
            $table->string('semester')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kuliahs');
    }
};
