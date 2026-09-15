<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename kolom status_bkkh -> status_bkhm pada tabel peminjaman
        foreach (['peminjaman_tempat', 'peminjaman_barang'] as $table) {
            if (Schema::hasColumn($table, 'status_bkkh') && ! Schema::hasColumn($table, 'status_bkhm')) {
                if (DB::connection()->getDriverName() === 'sqlite') {
                    DB::statement("ALTER TABLE {$table} RENAME COLUMN status_bkkh TO status_bkhm");
                } else {
                    DB::statement("ALTER TABLE {$table} CHANGE status_bkkh status_bkhm ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending'");
                }
            }
        }

        // Rapikan nilai teks status_akhir
        foreach (['peminjaman_tempat', 'peminjaman_barang'] as $table) {
            if (Schema::hasColumn($table, 'status_akhir')) {
                DB::table($table)->where('status_akhir', 'Proses BKKH')->update(['status_akhir' => 'Proses BKHM']);
                DB::table($table)->where('status_akhir', 'Ditolak BKKH')->update(['status_akhir' => 'Ditolak BKHM']);
            }
        }

        // Rename role bkh -> bkhm
        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'bkh')->update(['name' => 'bkhm']);
        }

        // Rename state workflow bkh_approved -> bkhm_approved
        if (Schema::hasTable('workflow_states')) {
            DB::table('workflow_states')->where('name', 'bkh_approved')->update(['name' => 'bkhm_approved']);
            DB::table('workflow_states')->where('name', 'bkh_review')->update(['name' => 'bkhm_review']);
            DB::table('workflow_states')->where('pic_role', 'BKKH')->update(['pic_role' => 'BKHM']);
            DB::table('workflow_states')->where('pic_role', 'BKKH / Bendahara')->update(['pic_role' => 'BKHM / Bendahara']);
        }

        if (Schema::hasTable('workflow_transitions')) {
            DB::table('workflow_transitions')->where('required_role', 'bkh')->update(['required_role' => 'bkhm']);
        }

        // Rapikan identitas user default
        if (Schema::hasTable('users')) {
            DB::table('users')->where('name', 'BKKH')->update(['name' => 'BKHM']);
            DB::table('users')->where('username', 'bkh')->update(['username' => 'bkhm']);
            DB::table('users')->where('email', 'bkh@test.com')->update(['email' => 'bkhm@test.com']);
        }
    }

    public function down(): void
    {
        foreach (['peminjaman_tempat', 'peminjaman_barang'] as $table) {
            if (Schema::hasColumn($table, 'status_bkhm') && ! Schema::hasColumn($table, 'status_bkkh')) {
                if (DB::connection()->getDriverName() === 'sqlite') {
                    DB::statement("ALTER TABLE {$table} RENAME COLUMN status_bkhm TO status_bkkh");
                } else {
                    DB::statement("ALTER TABLE {$table} CHANGE status_bkhm status_bkkh ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending'");
                }
            }
        }

        foreach (['peminjaman_tempat', 'peminjaman_barang'] as $table) {
            if (Schema::hasColumn($table, 'status_akhir')) {
                DB::table($table)->where('status_akhir', 'Proses BKHM')->update(['status_akhir' => 'Proses BKKH']);
                DB::table($table)->where('status_akhir', 'Ditolak BKHM')->update(['status_akhir' => 'Ditolak BKKH']);
            }
        }

        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'bkhm')->update(['name' => 'bkh']);
        }

        if (Schema::hasTable('workflow_states')) {
            DB::table('workflow_states')->where('name', 'bkhm_approved')->update(['name' => 'bkh_approved']);
            DB::table('workflow_states')->where('name', 'bkhm_review')->update(['name' => 'bkh_review']);
            DB::table('workflow_states')->where('pic_role', 'BKHM')->update(['pic_role' => 'BKKH']);
            DB::table('workflow_states')->where('pic_role', 'BKHM / Bendahara')->update(['pic_role' => 'BKKH / Bendahara']);
        }

        if (Schema::hasTable('workflow_transitions')) {
            DB::table('workflow_transitions')->where('required_role', 'bkhm')->update(['required_role' => 'bkh']);
        }

        if (Schema::hasTable('users')) {
            DB::table('users')->where('name', 'BKHM')->update(['name' => 'BKKH']);
            DB::table('users')->where('username', 'bkhm')->update(['username' => 'bkh']);
            DB::table('users')->where('email', 'bkhm@test.com')->update(['email' => 'bkh@test.com']);
        }
    }
};
