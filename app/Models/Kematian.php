<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kematian extends Model
{
    protected $table = 'kematian';

    protected $fillable = [
        'umat_id',
        'tanggal_meninggal',
        'tempat_meninggal',
        'tanggal_pemakaman',
        'tempat_pemakaman',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_meninggal' => 'date',
        'tanggal_pemakaman' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function umat(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'umat_id');
    }
}