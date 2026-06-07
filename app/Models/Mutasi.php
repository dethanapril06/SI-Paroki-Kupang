<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mutasi extends Model
{
    protected $table = 'mutasi';

    protected $fillable = [
        'jenis',
        'tanggal',
        'keterangan',
        'status',
        'pemohon_umat_id',
        'diproses_oleh_user_id',
        'catatan_admin',
        'diproses_pada',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'diproses_pada' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // ISA Child Relations
    // -------------------------------------------------------------------------

    public function mutasiUmat(): HasOne
    {
        return $this->hasOne(MutasiUmat::class, 'mutasi_id');
    }

    public function mutasiKeluarga(): HasOne
    {
        return $this->hasOne(MutasiKeluarga::class, 'mutasi_id');
    }

    public function mutasiAgama(): HasOne
    {
        return $this->hasOne(MutasiAgama::class, 'mutasi_id');
    }

    // -------------------------------------------------------------------------
    // Helper: ambil child sesuai discriminator 'jenis'
    // -------------------------------------------------------------------------

    /**
     * Mengembalikan relasi child yang sesuai berdasarkan nilai 'jenis'.
     * Contoh penggunaan: $mutasi->detail()
     */
    public function detail(): HasOne
    {
        return match ($this->jenis) {
            'umat'     => $this->mutasiUmat(),
            'keluarga' => $this->mutasiKeluarga(),
            'agama'    => $this->mutasiAgama(),
        };
    }

    // -------------------------------------------------------------------------
    // Approval Relations
    // -------------------------------------------------------------------------

    /** Umat yang mengajukan request (null = dibuat langsung oleh sekretariat) */
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'pemohon_umat_id');
    }

    /** User (sekretariat) yang memproses (approve/reject) */
    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh_user_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDitolak(Builder $query): Builder
    {
        return $query->where('status', 'ditolak');
    }

    // -------------------------------------------------------------------------
    // Status Helpers
    // -------------------------------------------------------------------------

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    /** Apakah mutasi ini diajukan oleh umat (bukan langsung sekretariat)? */
    public function dariRequest(): bool
    {
        return $this->pemohon_umat_id !== null;
    }
}