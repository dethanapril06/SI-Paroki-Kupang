<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class MutasiAgama extends Model
{
    protected $table = 'mutasi_agama';

    /**
     * PK = FK ke mutasi (pola ISA), bukan auto-increment.
     */
    public $incrementing = false;
    public $timestamps   = false;

    protected $primaryKey = 'mutasi_id';

    protected $fillable = [
        'mutasi_id',
        'umat_id',
        'agama_asal',
        'agama_tujuan',
    ];

    // -------------------------------------------------------------------------
    // Boot: enforce konsistensi dengan parent discriminator
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (MutasiAgama $model) {
            $mutasi = Mutasi::findOrFail($model->mutasi_id);

            throw_if(
                $mutasi->jenis !== 'agama',
                LogicException::class,
                "Mutasi ID {$model->mutasi_id} berjenis '{$mutasi->jenis}', bukan 'agama'."
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
        return $this->belongsTo(Umat::class, 'umat_id')->withTrashed();
    }
}