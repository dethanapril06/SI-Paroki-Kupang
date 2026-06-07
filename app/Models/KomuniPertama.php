<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomuniPertama extends Model
{

    protected $table = 'komuni_pertama';

    protected $fillable = [
        'sakramen_id',
    ];

    // ------------------------------------------------------------------
    // Relasi ke parent
    // ------------------------------------------------------------------
    public function sakramen(): BelongsTo
    {
        return $this->belongsTo(Sakramen::class, 'sakramen_id');
    }

    // ------------------------------------------------------------------
    // Shortcut: akses umat lewat sakramen
    // ------------------------------------------------------------------
    public function getUmatAttribute(): ?Umat
    {
        return $this->sakramen?->umat;
    }
}