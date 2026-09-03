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

#[Fillable(['name', 'email', 'password', 'username', 'status_akun', 'saldo', 'saldo_awal', 'foto_profil', 'logo_ormawa', 'nama_ketua', 'nama_sekretaris', 'nama_bendahara', 'ttd_ketua', 'ttd_sekretaris', 'ttd_bendahara', 'alamat', 'telepon'])]
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
        ];
    }

    public function pengajuans() { return $this->hasMany(Pengajuan::class, 'user_id'); }

    public function saldoHistori()
    {
        return $this->hasMany(SaldoHistori::class);
    }

    public function perubahanSaldo()
    {
        return $this->hasMany(SaldoHistori::class, 'actor_id');
    }
}
