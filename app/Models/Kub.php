<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kub extends Model
{
    use HasFactory;

    protected $table = 'kub';

    protected $fillable = ['nama', 'ketua_umat_id', 'wilayah_id'];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    /** Ketua KUB (seorang umat) */
    public function ketuaUmat(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'ketua_umat_id');
    }

    public function keluarga(): HasMany
    {
        return $this->hasMany(Keluarga::class);
    }
}
