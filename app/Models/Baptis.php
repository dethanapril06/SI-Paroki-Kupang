<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Baptis extends Model
{

    protected $table = 'baptis';

    protected $fillable = [
        'sakramen_id',
        'sumber_baptis',
        'klerus_id',
        'nama_pemberi_protestan',
        'nama_gereja_protestan',
        'tgl_baptis',
        'tgl_diterima_katolik',
        'nama_baptis',
        'bapak_baptis_id',
        'bapak_baptis_nama',
        'ibu_baptis_id',
        'ibu_baptis_nama',
    ];

    protected $casts = [
        'tgl_baptis'          => 'date',
        'tgl_diterima_katolik' => 'date',
    ];

    // ------------------------------------------------------------------
    // Enum helper
    // ------------------------------------------------------------------
    const SUMBER = [
        'KATOLIK'   => 'Katolik',
        'PROTESTAN' => 'Protestan',
    ];

    // ------------------------------------------------------------------
    // Relasi ke parent
    // ------------------------------------------------------------------
    public function sakramen(): BelongsTo
    {
        return $this->belongsTo(Sakramen::class, 'sakramen_id');
    }

    // ------------------------------------------------------------------
    // Pemberi baptis
    // Jika KATOLIK → klerus terdaftar
    // Jika PROTESTAN → nama manual
    // ------------------------------------------------------------------
    public function klerus(): BelongsTo
    {
        return $this->belongsTo(Klerus::class, 'klerus_id');
    }

    // ------------------------------------------------------------------
    // Wali baptis
    // Tiap wali punya dua jalur: FK ke umat atau nama manual
    // ------------------------------------------------------------------
    public function bapakBaptis(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'bapak_baptis_id');
    }

    public function ibuBaptis(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'ibu_baptis_id');
    }

    // ------------------------------------------------------------------
    // Accessor: nama pemberi baptis (otomatis pilih sesuai sumber)
    // ------------------------------------------------------------------
    public function getNamaPemberiAttribute(): string
    {
        if ($this->sumber_baptis === 'KATOLIK') {
            return $this->klerus?->nama ?? '-';
        }

        return $this->nama_pemberi_protestan ?? '-';
    }

    // ------------------------------------------------------------------
    // Accessor: nama bapak baptis (FK atau manual)
    // ------------------------------------------------------------------
    public function getNamaBapakBaptisAttribute(): string
    {
        return $this->bapakBaptis?->nama_lengkap ?? $this->bapak_baptis_nama ?? '-';
    }

    // ------------------------------------------------------------------
    // Accessor: nama ibu baptis (FK atau manual)
    // ------------------------------------------------------------------
    public function getNamaIbuBaptisAttribute(): string
    {
        return $this->ibuBaptis?->nama_lengkap ?? $this->ibu_baptis_nama ?? '-';
    }

    // ------------------------------------------------------------------
    // Helper: apakah baptis dari Protestan?
    // ------------------------------------------------------------------
    public function dariProtestan(): bool
    {
        return $this->sumber_baptis === 'PROTESTAN';
    }

    // ------------------------------------------------------------------
    // Helper: apakah sudah resmi diterima Katolik?
    // ------------------------------------------------------------------
    public function sudahDiterimaKatolik(): bool
    {
        return $this->dariProtestan() && $this->tgl_diterima_katolik !== null;
    }
}