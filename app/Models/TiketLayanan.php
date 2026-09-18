<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketLayanan extends Model
{
    use HasFactory;

    protected $table = 'tiket_layanans';

    protected $fillable = [
        'kode_tiket',
        'kategori',
        'sub_kategori',
        'nim',
        'nama_mahasiswa',
        'email',
        'no_hp',
        'prodi',
        'judul',
        'isi',
        'lampiran',
        'catatan_bpm',
        'catatan_bkhm',
        'diteruskan_ke_bkhm_at',
        'topik_konseling',
        'metode_konseling',
        'deskripsi_masalah',
        'tanggapan_bkhm',
        'jadwal_temu',
        'lokasi_atau_link',
        'konfirmasi_mahasiswa',
        'catatan_konfirmasi_mahasiswa',
        'konfirmasi_at',
        'nama_kegiatan',
        'penyelenggara',
        'tingkat',
        'capaian',
        'tanggal_kegiatan',
        'estimasi_biaya',
        'lampiran_bukti',
        'tampil_ke_publik',
        'status',
    ];

    protected $casts = [
        'jadwal_temu' => 'datetime',
        'konfirmasi_at' => 'datetime',
        'tanggal_kegiatan' => 'date',
        'diteruskan_ke_bkhm_at' => 'datetime',
        'tampil_ke_publik' => 'boolean',
        'estimasi_biaya' => 'decimal:2',
        'deskripsi_masalah' => 'encrypted',
        'tanggapan_bkhm' => 'encrypted',
        'catatan_konfirmasi_mahasiswa' => 'encrypted',
    ];

    /**
     * Generate Kode Tiket unik format seragam: SKIN-TKT-YYYY-XXXX
     */
    public static function generateKodeTiket(): string
    {
        $year = date('Y');
        $prefix = "SKIN-TKT-{$year}-";

        $last = self::where('kode_tiket', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if (! $last) {
            return $prefix . '0001';
        }

        $lastNum = (int) substr($last->kode_tiket, -4);
        return $prefix . str_pad((string) ($lastNum + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Label status yang ramah bagi pengguna non-IT.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Peninjauan',
            'direkap_bpm' => 'Dihimpun oleh BPM',
            'diteruskan_ke_bkhm' => 'Diteruskan ke BKHM',
            'ditinjau_bkhm' => 'Sedang Ditinjau BKHM',
            'jadwal_ditentukan' => 'Jadwal Temu Ditentukan',
            'diverifikasi_bkhm' => 'Diverifikasi BKHM',
            'diproses_bkhm' => 'Sedang Diproses BKHM',
            'ditindaklanjuti' => 'Telah Ditindaklanjuti',
            'disetujui' => 'Disetujui',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak / Belum Memenuhi Syarat',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Warna badge status (Tailwind).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'direkap_bpm', 'ditinjau_bkhm', 'diverifikasi_bkhm', 'diproses_bkhm' => 'bg-blue-100 text-blue-800 border-blue-300',
            'diteruskan_ke_bkhm' => 'bg-purple-100 text-purple-800 border-purple-300',
            'jadwal_ditentukan' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'disetujui', 'ditindaklanjuti', 'selesai' => 'bg-green-100 text-green-800 border-green-300',
            'ditolak' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    /**
     * Tanggapan resmi pengelola terkonsolidasi (BKHM / BPM)
     */
    public function getTanggapanResmiAttribute(): ?string
    {
        return $this->tanggapan_bkhm ?: ($this->catatan_bkhm ?: $this->catatan_bpm);
    }
}
