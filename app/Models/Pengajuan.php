<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'dana_diajukan',
        'tanggal_pengajuan',
        'file_proposal',
        'file_lpj',
        'tanggal_upload_lpj',
        'workflow_state_id',
        'rejected_from_state_id',
        'catatan_revisi',
        'nomor_surat',
        'unique_code',
        'notif_cair_terlihat',
        'evaluasi_termin_ok',
        'terakhir_diingatkan_at',
        'jumlah_nudge',
        'tanggal_mulai_kegiatan',
        'tanggal_selesai_kegiatan',
        'program_kerja_id',
    ];

    protected $casts = [
        'evaluasi_termin_ok' => 'boolean',
        'terakhir_diingatkan_at' => 'datetime',
        'jumlah_nudge' => 'integer',
        'tanggal_mulai_kegiatan' => 'date',
        'tanggal_selesai_kegiatan' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(WorkflowState::class, 'workflow_state_id');
    }

    public function histori(): HasMany
    {
        return $this->hasMany(HistoriStatus::class);
    }

    /**
     * FR-011: pesan komunikasi/follow-up pengajuan.
     */
    public function komunikasi(): HasMany
    {
        return $this->hasMany(KomunikasiPengajuan::class)->orderBy('created_at');
    }

    public function dana(): HasOne
    {
        return $this->hasOne(Dana::class)->latestOfMany();
    }

    /**
     * Seluruh termin pencairan dana untuk pengajuan ini.
     */
    public function danaList(): HasMany
    {
        return $this->hasMany(Dana::class)->orderBy('termin_ke');
    }

    /**
     * Total dana yang telah dicairkan (semua termin).
     */
    public function totalDicairkan(): float
    {
        return (float) $this->danaList()->sum('nominal_cair');
    }

    /**
     * Nomor termin berikutnya (1 jika belum pernah dicairkan).
     */
    public function terminBerikutnya(): int
    {
        return ((int) $this->danaList()->max('termin_ke')) + 1;
    }

    /**
     * Role lembaga pemeriksa yang bertanggung jawab atas pengajuan pada tahapan saat ini.
     */
    public function targetRoleNudge(): ?string
    {
        return match ($this->state?->name) {
            WorkflowState::SUBMITTED => 'bem',
            WorkflowState::BEM_APPROVED => 'bpm',
            WorkflowState::BPM_APPROVED => 'bkhm',
            WorkflowState::BKHM_APPROVED => 'wr3',
            WorkflowState::WR3_APPROVED, WorkflowState::TO_TREASURER => 'bendahara',
            WorkflowState::LPJ_SUBMITTED => 'bkhm',
            WorkflowState::LPJ_WR3_REVIEW => 'wr3',
            default => null,
        };
    }

    /**
     * Nama lembaga yang sedang memproses pengajuan.
     */
    public function targetLembagaLabel(): string
    {
        return match ($this->targetRoleNudge()) {
            'bem' => 'BEM ITG',
            'bpm' => 'BPM ITG',
            'bkhm' => 'BKHM',
            'wr3' => 'Wakil Rektor III (WR3)',
            'bendahara' => 'Bendahara',
            default => 'Lembaga Pemeriksa',
        };
    }

    /**
     * Cek apakah pengajuan masih dalam periode cooldown 12 jam sejak pengingat terakhir.
     */
    public function apakahDalamCooldown(): bool
    {
        if (! $this->terakhir_diingatkan_at) {
            return false;
        }

        return $this->terakhir_diingatkan_at->copy()->addHours(12)->isFuture();
    }

    /**
     * Menghitung sisa waktu cooldown dalam format ramah pengguna (contoh: "5 jam 20 menit").
     */
    public function sisaWaktuCooldown(): ?string
    {
        if (! $this->apakahDalamCooldown()) {
            return null;
        }

        $berakhirAt = $this->terakhir_diingatkan_at->copy()->addHours(12);
        $totalMenit = (int) max(0, now()->diffInMinutes($berakhirAt, false));
        $jam = intdiv($totalMenit, 60);
        $menit = $totalMenit % 60;

        if ($jam > 0) {
            return "{$jam} jam {$menit} menit";
        }

        return "{$menit} menit";
    }

    /**
     * Cek apakah pengajuan dapat dikirimkan pengingat (nudge) saat ini.
     */
    public function bisaDiingatkan(): bool
    {
        if (! $this->targetRoleNudge()) {
            return false;
        }

        return ! $this->apakahDalamCooldown();
    }

    /**
     * Program kerja tahunan yang terkait dengan pengajuan ini (jika ada).
     */
    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }

    /**
     * Menghitung status urgensi pelaksanaan kegiatan (H-X) terhadap hari ini.
     */
    public function statusUrgensi(): ?array
    {
        if (! $this->tanggal_mulai_kegiatan) {
            return null;
        }

        $today = now()->startOfDay();
        $eventDate = $this->tanggal_mulai_kegiatan->copy()->startOfDay();
        $diffDays = (int) $today->diffInDays($eventDate, false);

        if ($diffDays < 0) {
            $daysAgo = abs($diffDays);
            return [
                'label' => "Hari-H Terlewat ({$daysAgo} hari lalu)",
                'badge_class' => 'bg-red-700 text-white font-bold',
                'is_urgent' => true,
                'days' => $diffDays,
            ];
        }

        if ($diffDays === 0) {
            return [
                'label' => 'Hari-H Kegiatan (Hari Ini!)',
                'badge_class' => 'bg-red-600 text-white font-bold animate-pulse',
                'is_urgent' => true,
                'days' => 0,
            ];
        }

        if ($diffDays <= 3) {
            return [
                'label' => "Mendesak: H-{$diffDays} Kegiatan",
                'badge_class' => 'bg-rose-100 text-rose-800 border border-rose-300 font-bold',
                'is_urgent' => true,
                'days' => $diffDays,
            ];
        }

        if ($diffDays <= 7) {
            return [
                'label' => "Perhatian: H-{$diffDays} Kegiatan",
                'badge_class' => 'bg-amber-100 text-amber-800 border border-amber-300 font-semibold',
                'is_urgent' => true,
                'days' => $diffDays,
            ];
        }

        return [
            'label' => "H-{$diffDays} Kegiatan",
            'badge_class' => 'bg-slate-100 text-slate-700 border border-slate-200',
            'is_urgent' => false,
            'days' => $diffDays,
        ];
    }
}


