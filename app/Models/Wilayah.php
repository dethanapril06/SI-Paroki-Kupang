<?php

namespace App\Models;

use App\Models\Kub;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = ['nama', 'ketua_umat_id', 'paroki_id', 'kuasi_id', 'stasi_id'];

    public function paroki(): BelongsTo
    {
        return $this->belongsTo(Paroki::class);
    }

    public function kuasi(): BelongsTo
    {
        return $this->belongsTo(Kuasi::class);
    }

    public function stasi(): BelongsTo
    {
        return $this->belongsTo(Stasi::class);
    }

    public function kub(): HasMany
    {
        return $this->hasMany(Kub::class);
    }

    /** Ketua Wilayah (seorang umat) */
    public function ketuaUmat(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'ketua_umat_id');
    }
}
