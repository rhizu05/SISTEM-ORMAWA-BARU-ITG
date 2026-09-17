<?php

namespace App\Console\Commands;

use App\Models\PeminjamanBarang;
use App\Services\NotifikasiService;
use Illuminate\Console\Command;

class NotifikasiBarangTerlambat extends Command
{
    protected $signature = 'notifikasi:barang-terlambat';

    protected $description = 'FR-022 §22 no.5: kirim notifikasi barang yang belum dikembalikan melewati batas waktu.';

    public function handle(): int
    {
        $terlambat = PeminjamanBarang::with('user')
            ->where('status_akhir', 'Sedang Digunakan')
            ->whereDate('tgl_selesai', '<', now()->toDateString())
            ->get();

        foreach ($terlambat as $p) {
            NotifikasiService::kirimKeRole(
                'sarpras',
                'Barang untuk kegiatan "' . $p->nama_kegiatan . '" belum dikembalikan (batas ' . $p->tgl_selesai . ').'
            );

            if ($p->user_id) {
                NotifikasiService::kirim(
                    $p->user_id,
                    'Batas pengembalian barang untuk kegiatan "' . $p->nama_kegiatan . '" telah lewat.'
                );
            }
        }

        $this->info('Notifikasi keterlambatan diproses: ' . $terlambat->count() . ' peminjaman.');

        return self::SUCCESS;
    }
}
