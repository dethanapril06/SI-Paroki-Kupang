<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stasi extends Model
{
    use HasFactory;

    protected $table = 'stasi';

    protected $fillable = ['nama', 'alamat', 'koordinator', 'paroki_id', 'kuasi_id'];

    public function paroki(): BelongsTo
    {
        return $this->belongsTo(Paroki::class);
    }

    public function kuasi(): BelongsTo
    {
        return $this->belongsTo(Kuasi::class);
    }

    public function wilayah(): HasMany
    {
        return $this->hasMany(Wilayah::class);
    }
}
