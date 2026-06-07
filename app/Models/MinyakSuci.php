<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class MinyakSuci extends Model
{

    protected $table = 'minyak_suci';

    protected $fillable = [
        'sakramen_id',
        'tempat_terima',
        'nama_pemberi',
        'keterangan_sebab',
    ];

    // ------------------------------------------------------------------
    // Relasi ke parent
    // ------------------------------------------------------------------
    public function sakramen(): BelongsTo
    {
        return $this->belongsTo(Sakramen::class, 'sakramen_id');
    }

    // ------------------------------------------------------------------
    // Accessor: nama pemberi
    // Klerus via sakramen jika ada, fallback ke nama manual
    // ------------------------------------------------------------------
    public function getNamaPemberiLengkapAttribute(): string
    {
        // Jika ada klerus terdaftar via sakramen, tampilkan nama klerus
        if ($this->sakramen?->klerus_id) {
            return $this->sakramen->klerus?->nama ?? '-';
        }

        // Fallback ke nama pemberi manual
        return $this->nama_pemberi ?? '-';
    }

    // ------------------------------------------------------------------
    // Shortcut: umat penerima (via sakramen)
    // ------------------------------------------------------------------
    public function getUmatAttribute(): ?Umat
    {
        return $this->sakramen?->umat;
    }
}