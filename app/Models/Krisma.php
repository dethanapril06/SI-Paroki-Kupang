<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Krisma extends Model
{

    protected $table = 'krisma';

    protected $fillable = [
        'sakramen_id',
        'uskup_id',
        'nama_krisma',
    ];

    // ------------------------------------------------------------------
    // Relasi ke parent
    // ------------------------------------------------------------------
    public function sakramen(): BelongsTo
    {
        return $this->belongsTo(Sakramen::class, 'sakramen_id');
    }

    // ------------------------------------------------------------------
    // Uskup yang menerimakan krisma (jabatan USKUP di tabel klerus)
    // ------------------------------------------------------------------
    public function uskup(): BelongsTo
    {
        return $this->belongsTo(Klerus::class, 'uskup_id');
    }

}