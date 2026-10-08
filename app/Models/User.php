<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'username', 'status_akun', 'saldo', 'saldo_awal', 'file_sk', 'nomor_sk', 'tanggal_sk', 'foto_profil', 'logo_ormawa', 'nama_ketua', 'nim_ketua', 'nama_sekretaris', 'nim_sekretaris', 'nama_bendahara', 'nim_bendahara', 'ttd_ketua', 'ttd_sekretaris', 'ttd_bendahara', 'alamat', 'telepon'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_sk' => 'date',
        ];
    }

    public function pengajuans() { return $this->hasMany(Pengajuan::class, 'user_id'); }

    public function saldoHistori()
    {
        return $this->hasMany(SaldoHistori::class);
    }

    /**
     * UI-011: notifikasi in-app milik pengguna.
     */
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function perubahanSaldo()
    {
        return $this->hasMany(SaldoHistori::class, 'actor_id');
    }

    /**
     * Q-SAR-04: apakah organisasi ini HIMA (di bawah program studi) sehingga
     * peminjaman fasilitasnya memerlukan persetujuan Prodi (di luar sistem).
     */
    public function isHima(): bool
    {
        return $this->hasRole('ormawa') && preg_match('/\bHIMA/i', $this->name ?? '') === 1;
    }

    public function suratPeringatans()
    {
        return $this->hasMany(SuratPeringatan::class, 'target_user_id');
    }

    public function letters()
    {
        return $this->hasMany(Letter::class, 'user_id');
    }

    public function skLetter()
    {
        return $this->hasOne(Letter::class, 'user_id')->where('type', 'sk_kepengurusan')->latestOfMany();
    }

    /**
     * Memeriksa apakah user memiliki avatar kustom (logo ormawa, foto profil, atau logo resmi institusi).
     */
    public function hasCustomAvatar(): bool
    {
        $path = $this->logo_ormawa ?: $this->foto_profil;
        if (!empty($path)) {
            return true;
        }

        return in_array($this->username, ['bkhm', 'admin', 'wr3', 'bendahara', 'sarpras']);
    }

    /**
     * Mendapatkan URL avatar / logo user, memprioritaskan logo_ormawa lalu foto_profil.
     */
    public function getAvatarUrlAttribute(): string
    {
        $path = $this->logo_ormawa ?: $this->foto_profil;

        if ($path) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path) || file_exists(public_path('storage/' . $path))) {
                return asset('storage/' . $path);
            }
            if (file_exists(public_path('images/' . $path))) {
                return asset('images/' . $path);
            }
        }

        // Fallback institusi kampus resmi ke logo ITG
        if (in_array($this->username, ['bkhm', 'admin', 'wr3', 'bendahara', 'sarpras']) && file_exists(public_path('images/logo_itg.png'))) {
            return asset('images/logo_itg.png');
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&background=EFF6FF&color=1E40AF&bold=true';
    }
}
