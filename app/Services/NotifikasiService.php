<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifikasiService
{
    /**
     * FR-025: kirim notifikasi in-app + email institusi.
     * Kanal email institusi adalah kanal eksternal utama (Q-MHS-03);
     * riwayat tetap tersimpan di sistem.
     */
    public static function kirim(int $userId, string $pesan): void
    {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        // In-app
        Notifikasi::create([
            'user_id' => $user->id,
            'pesan' => $pesan,
            'status_baca' => 'belum',
        ]);

        // Email institusi (kanal eksternal utama)
        if (! empty($user->email)) {
            try {
                Mail::raw($pesan, function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Notifikasi SKIN');
                });
            } catch (\Throwable $e) {
                // Jangan gagalkan alur utama bila pengiriman email bermasalah.
                Log::warning('Gagal mengirim email notifikasi: ' . $e->getMessage());
            }
        }
    }

    /**
     * Kirim ke banyak pengguna berdasarkan role tertentu.
     */
    public static function kirimKeRole(string $role, string $pesan): void
    {
        User::role($role)->get()->each(function (User $user) use ($pesan) {
            self::kirim($user->id, $pesan);
        });
    }
}
