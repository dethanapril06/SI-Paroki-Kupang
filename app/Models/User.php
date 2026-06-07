<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'umat_id', 'klerus_id', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================================
    // Relasi
    // =========================================================================

    /**
     * Profil umat yang terhubung ke akun ini.
     * Berlaku untuk role: umat, ketua_kub, ketua_kategorial
     */
    public function umat(): BelongsTo
    {
        return $this->belongsTo(Umat::class);
    }

    /**
     * Profil klerus yang terhubung ke akun ini.
     * Berlaku untuk role: pastor
     */
    public function klerus(): BelongsTo
    {
        return $this->belongsTo(Klerus::class);
    }

    /**
     * Semua role yang dimiliki user ini (many-to-many via user_roles).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    // =========================================================================
    // Helper: cek role
    // =========================================================================

    /**
     * Cek apakah user memiliki role tertentu.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    /**
     * Cek apakah user memiliki salah satu dari beberapa role.
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles->pluck('name')->intersect($roleNames)->isNotEmpty();
    }

    /**
     * Apakah user ini adalah umat (termasuk ketua-ketua yang juga umat)?
     */
    public function isUmat(): bool
    {
        return $this->hasAnyRole(['umat', 'ketua_kub', 'ketua_kategorial']);
    }

    /**
     * Apakah user ini memegang jabatan ketua (KUB dan/atau kategorial)?
     */
    public function isKetua(): bool
    {
        return $this->hasAnyRole(['ketua_kub', 'ketua_kategorial']);
    }

    /**
     * Apakah user ini ketua KUB?
     */
    public function isKetuaKub(): bool
    {
        return $this->hasRole('ketua_kub');
    }

    /**
     * Apakah user ini ketua kategorial?
     */
    public function isKetuaKategorial(): bool
    {
        return $this->hasRole('ketua_kategorial');
    }

    /**
     * Apakah user ini seorang pastor (klerus)?
     */
    public function isPastor(): bool
    {
        return $this->hasRole('pastor');
    }

    /**
     * Apakah user ini sekretariat?
     */
    public function isSekretariat(): bool
    {
        return $this->hasRole('sekretariat');
    }

    /**
     * Apakah user ini dewan pastoral?
     */
    public function isDewanPastoral(): bool
    {
        return $this->hasRole('dewan_pastoral');
    }

    // =========================================================================
    // Helper: cek status akun
    // =========================================================================

    /** Akun masih menunggu persetujuan sekretariat */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /** Akun sudah aktif dan bisa login */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** Akun ditolak oleh sekretariat */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
