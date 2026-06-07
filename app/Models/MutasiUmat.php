<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class MutasiUmat extends Model
{
    protected $table = 'mutasi_umat';

    /**
     * PK = FK ke mutasi (pola ISA), bukan auto-increment.
     */
    public $incrementing = false;
    public $timestamps   = false;

    protected $primaryKey = 'mutasi_id';

    protected $fillable = [
        'mutasi_id',
        'umat_id',
        'sub_jenis',
        'nomor_surat',
        'keluarga_asal_id',
        'keluarga_tujuan_id',
        'kub_asal_id',
        'kub_tujuan_id',
        'wilayah_asal_id',
        'wilayah_tujuan_id',
        'paroki_asal_id',
        'paroki_tujuan_id',
        'keuskupan_asal_id',
        'keuskupan_tujuan_id',
        'alamat_baru',
        'status_tempat_tinggal_baru',
    ];

    // -------------------------------------------------------------------------
    // Boot: enforce konsistensi dengan parent discriminator
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (MutasiUmat $model) {
            $mutasi = Mutasi::findOrFail($model->mutasi_id);

            throw_if(
                $mutasi->jenis !== 'umat',
                LogicException::class,
                "Mutasi ID {$model->mutasi_id} berjenis '{$mutasi->jenis}', bukan 'umat'."
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

    public function umat(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'umat_id');
    }

    public function keluargaAsal(): BelongsTo
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_asal_id');
    }

    public function keluargaTujuan(): BelongsTo
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_tujuan_id');
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