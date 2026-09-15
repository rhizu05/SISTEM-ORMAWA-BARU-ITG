<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Q-SAR-01: gabungkan role sarpras_ruangan & sarpras_barang menjadi 'sarpras'.
        if (! Schema::hasTable('roles')) {
            return;
        }

        $target = DB::table('roles')->where('name', 'sarpras')->value('id');

        foreach (['sarpras_ruangan', 'sarpras_barang'] as $oldName) {
            $oldId = DB::table('roles')->where('name', $oldName)->value('id');
            if (! $oldId) {
                continue;
            }

            if (! $target) {
                // Belum ada role sarpras: rename role pertama yang ditemukan.
                DB::table('roles')->where('id', $oldId)->update(['name' => 'sarpras']);
                $target = $oldId;
                continue;
            }

            // Pindahkan assignment pengguna ke role sarpras, lalu hapus role lama.
            $now = now();
            $assignments = DB::table('model_has_roles')->where('role_id', $oldId)->get();
            foreach ($assignments as $a) {
                $exists = DB::table('model_has_roles')
                    ->where('role_id', $target)
                    ->where('model_type', $a->model_type)
                    ->where('model_id', $a->model_id)
                    ->exists();

                if (! $exists) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $target,
                        'model_type' => $a->model_type,
                        'model_id' => $a->model_id,
                    ]);
                }
            }

            DB::table('model_has_roles')->where('role_id', $oldId)->delete();
            DB::table('role_has_permissions')->where('role_id', $oldId)->delete();
            DB::table('roles')->where('id', $oldId)->delete();
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan: konsolidasi role satu arah.
    }
};
