<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_panitia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal_otomatis')->onDelete('cascade');
            $table->string('jabatan');
            $table->string('nama_mahasiswa');
            $table->string('nim')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_panitia');
    }
};
