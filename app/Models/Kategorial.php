<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kategorial extends Model
{
    use HasFactory;

    protected $table = 'kategorial';

    protected $fillable = ['nama', 'ketua_umat_id'];

    /** Ketua Kategorial (seorang umat) */
    public function ketuaUmat(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'ketua_umat_id');
    }

    /** Data anggota kategorial sebagai model pivot */
    public function anggotaKategorial(): HasMany
    {
        return $this->hasMany(AnggotaKategorial::class, 'kategorial_id');
    }

    /** Semua anggota umat dalam kategorial ini (dengan jabatan, tgl bergabung, status) */
    public function anggota(): BelongsToMany
    {
        return $this->belongsToMany(Umat::class, 'anggota_kategorial')
            ->using(AnggotaKategorial::class)
            ->withPivot(['id', 'jabatan', 'tanggal_bergabung', 'status'])
            ->withTimestamps();
    }
}

