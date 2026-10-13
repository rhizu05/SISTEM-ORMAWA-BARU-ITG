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
        'jenis_kepesertaan',
        'nama_tim',
        'dosen_pembimbing',
        'anggota_tim',
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
        'url_penyelenggara',
        'tingkat',
        'capaian',
        'tanggal_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'estimasi_biaya',
        'lampiran_bukti',
        'foto_penyerahan',
        'tampil_ke_publik',
        'status',
    ];

    protected $casts = [
        'jadwal_temu' => 'datetime',
        'konfirmasi_at' => 'datetime',
        'tanggal_kegiatan' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'diteruskan_ke_bkhm_at' => 'datetime',
        'tampil_ke_publik' => 'boolean',
        'estimasi_biaya' => 'decimal:2',
        'deskripsi_masalah' => 'encrypted',
        'tanggapan_bkhm' => 'encrypted',
        'catatan_konfirmasi_mahasiswa' => 'encrypted',
        'anggota_tim' => 'array',
    ];

    public function isKelompok(): bool
    {
        return $this->jenis_kepesertaan === 'kelompok';
    }

    public function getDaftarAnggotaAttribute(): array
    {
        return is_array($this->anggota_tim) ? $this->anggota_tim : [];
    }

    public function getTotalPesertaAttribute(): int
    {
        return 1 + count($this->daftar_anggota);
    }

    /**
     * Generate Kode Tiket unik format berkeamanan tinggi (High-Entropy Non-Sequential): SKIN-TKT-YYYY-XXXXXX
     * Menggunakan karakter alfanumerik kapital non-ambigu untuk mencegah brute-force/enumerasi.
     */
    public static function generateKodeTiket(): string
    {
        $year = date('Y');
        $prefix = "SKIN-TKT-{$year}-";
        // Karakter non-ambigu (tanpa 0, O, 1, I)
        $charset = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $randomSuffix = '';
            for ($i = 0; $i < 6; $i++) {
                $randomSuffix .= $charset[random_int(0, strlen($charset) - 1)];
            }
            $kode = $prefix . $randomSuffix;
        } while (self::where('kode_tiket', $kode)->exists());

        return $kode;
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
            'direkap_bpm', 'ditinjau_bkhm', 'diverifikasi_bkhm', 'diproses_bkhm' => 'bg-slate-100 text-slate-700 border-slate-300',
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

    /**
     * Format rentang waktu pelaksanaan kegiatan untuk pelaporan Dikti & tampilan publik
     */
    public function getRentangTanggalAttribute(): string
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            if ($this->tanggal_mulai->format('Y-m-d') === $this->tanggal_selesai->format('Y-m-d')) {
                return $this->tanggal_mulai->translatedFormat('d F Y');
            }
            if ($this->tanggal_mulai->format('Y-m') === $this->tanggal_selesai->format('Y-m')) {
                return $this->tanggal_mulai->translatedFormat('d') . ' - ' . $this->tanggal_selesai->translatedFormat('d F Y');
            }
            return $this->tanggal_mulai->translatedFormat('d M Y') . ' - ' . $this->tanggal_selesai->translatedFormat('d M Y');
        }

        if ($this->tanggal_mulai) {
            return $this->tanggal_mulai->translatedFormat('d F Y');
        }

        if ($this->tanggal_kegiatan) {
            return $this->tanggal_kegiatan->translatedFormat('d F Y');
        }

        return $this->created_at ? $this->created_at->translatedFormat('d F Y') : '-';
    }
}
