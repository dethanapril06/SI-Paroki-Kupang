<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pernikahan extends Model
{
    protected $table = 'pernikahan';

    protected $fillable = [
        'sakramen_id',
        'pasangan_id',
        'pasangan_nama',
        'pasangan_agama',
        'jenis_pernikahan',
        'izin_beda_gereja',
        'dispensasi',
        'tanggal_nikah_katolik',
        'tanggal_catatan_sipil',
    ];

    protected $casts = [
        'izin_beda_gereja'      => 'boolean',
        'dispensasi'            => 'boolean',
        'tanggal_nikah_katolik' => 'date',
        'tanggal_catatan_sipil' => 'date',
    ];

    const JENIS = [
        'KATOLIK_KATOLIK'     => 'Katolik - Katolik',
        'KATOLIK_PROTESTAN'   => 'Katolik - Protestan',
        'KATOLIK_ISLAM'       => 'Katolik - Islam',
        'KATOLIK_HINDU'       => 'Katolik - Hindu',
        'KATOLIK_BUDDHA'      => 'Katolik - Buddha',
        'KATOLIK_KONGHUCU'    => 'Katolik - Konghucu',
        'KATOLIK_KEPERCAYAAN' => 'Katolik - Kepercayaan',
    ];

    public function sakramen(): BelongsTo
    {
        return $this->belongsTo(Sakramen::class, 'sakramen_id');
    }

    public function getUmatAttribute(): ?Umat
    {
        return $this->sakramen?->umat;
    }

    public function pasangan(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'pasangan_id');
    }

    public function getNamaPasanganAttribute(): string
    {
        return $this->pasangan?->nama ?? $this->pasangan_nama ?? '-';
    }

    public function getAgamaPasanganAttribute(): string
    {
        // Jika pasangan terdaftar di sistem (pasangan_id ada), berarti dia Katolik
        if ($this->pasangan_id) {
            return 'Katholik';
        }

        // Jika pasangan diisi manual (beda agama), ambil dari field pasangan_agama
        return $this->pasangan_agama ?? '-';
    }

    public function getKlerusAttribute(): ?Klerus
    {
        return $this->sakramen?->klerus;
    }
}