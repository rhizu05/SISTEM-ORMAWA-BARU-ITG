<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_rapat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Penyelenggara
            $table->string('judul_rapat');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_rapat');
            $table->time('jam_rapat');
            $table->string('lokasi');
            $table->string('link_meeting')->nullable();
            
            // JSON column untuk peserta: ["bem", "bpm", "ormawa"]
            $table->json('target_peserta')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_rapat');
    }
};
