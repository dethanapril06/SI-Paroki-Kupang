<?php

namespace App\Models;

use App\Models\Keuskupan;
use App\Models\Klerus;
use App\Models\Kuasi;
use App\Models\Stasi;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paroki extends Model
{
    use HasFactory;

    protected $table = 'paroki';

    protected $fillable = ['nama', 'alamat', 'keuskupan_id', 'klerus_id'];

    public function keuskupan(): BelongsTo
    {
        return $this->belongsTo(Keuskupan::class);
    }

    public function klerus(): BelongsTo
    {
        return $this->belongsTo(Klerus::class);
    }

    public function kuasi(): HasMany
    {
        return $this->hasMany(Kuasi::class);
    }

    public function stasi(): HasMany
    {
        return $this->hasMany(Stasi::class);
    }

    public function wilayah(): HasMany
    {
        return $this->hasMany(Wilayah::class);
    }
}
