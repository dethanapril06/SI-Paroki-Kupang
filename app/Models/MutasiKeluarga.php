<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class MutasiKeluarga extends Model
{
    protected $table = 'mutasi_keluarga';

    /**
     * PK = FK ke mutasi (pola ISA), bukan auto-increment.
     */
    public $incrementing = false;
    public $timestamps   = false;

    protected $primaryKey = 'mutasi_id';

    protected $fillable = [
        'mutasi_id',
        'keluarga_id',
        'sub_jenis',
        'nomor_surat',
        'kub_asal_id',
        'kub_tujuan_id',
        'wilayah_asal_id',
        'wilayah_tujuan_id',
        'paroki_asal_id',
        'paroki_tujuan_id',
        'keuskupan_asal_id',
        'keuskupan_tujuan_id',
    ];

    // -------------------------------------------------------------------------
    // Boot: enforce konsistensi dengan parent discriminator
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (MutasiKeluarga $model) {
            $mutasi = Mutasi::findOrFail($model->mutasi_id);

            throw_if(
                $mutasi->jenis !== 'keluarga',
                LogicException::class,
                "Mutasi ID {$model->mutasi_id} berjenis '{$mutasi->jenis}', bukan 'keluarga'."
            );
        });
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function mutasi(): BelongsTo
    {
        return $this->belongsTo(Mutasi::class, 'mutasi_id');
    }

    public function keluarga(): BelongsTo
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id');
    }

    public function kubAsal(): BelongsTo
    {
        return $this->belongsTo(Kub::class, 'kub_asal_id');
    }

    public function kubTujuan(): BelongsTo
    {
        return $this->belongsTo(Kub::class, 'kub_tujuan_id');
    }

    public function wilayahAsal(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_asal_id');
    }

    public function wilayahTujuan(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_tujuan_id');
    }

    public function parokiAsal(): BelongsTo
    {
        return $this->belongsTo(Paroki::class, 'paroki_asal_id');
    }

    public function parokiTujuan(): BelongsTo
    {
        return $this->belongsTo(Paroki::class, 'paroki_tujuan_id');
    }

    public function keuskupanAsal(): BelongsTo
    {
        return $this->belongsTo(Keuskupan::class, 'keuskupan_asal_id');
    }

    public function keuskupanTujuan(): BelongsTo
    {
        return $this->belongsTo(Keuskupan::class, 'keuskupan_tujuan_id');
    }
}