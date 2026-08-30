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
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ormawa id
            $table->string('nama_kegiatan');
            $table->decimal('dana_diajukan', 15, 2);
            $table->date('tanggal_pengajuan');
            $table->string('file_proposal')->nullable();
            $table->string('file_lpj')->nullable();
            $table->date('tanggal_upload_lpj')->nullable();
            
            // Dynamic workflow state
            $table->foreignId('workflow_state_id')->constrained('workflow_states');
            
            $table->text('catatan_revisi')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('unique_code')->unique()->nullable();
            $table->boolean('notif_cair_terlihat')->default(false);
            $table->timestamps();
        });

        Schema::create('histori_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // who made the change
            $table->foreignId('workflow_state_id')->constrained('workflow_states'); // new state
            $table->text('catatan')->nullable();
            $table->string('unique_code')->nullable();
            $table->timestamps();
        });

        Schema::create('dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->onDelete('cascade');
            $table->decimal('nominal_cair', 15, 2);
            $table->date('tanggal_cair');
            $table->timestamps();
        });

        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('pesan');
            $table->enum('status_baca', ['belum', 'sudah'])->default('belum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('dana');
        Schema::dropIfExists('histori_status');
        Schema::dropIfExists('pengajuan');
    }
};
