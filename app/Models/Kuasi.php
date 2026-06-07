<?php

namespace App\Models;

use App\Models\Klerus;
use App\Models\Paroki;
use App\Models\Stasi;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuasi extends Model
{
    use HasFactory;

    protected $table = 'kuasi';

    protected $fillable = ['nama', 'alamat', 'paroki_id', 'klerus_id'];

    public function paroki(): BelongsTo
    {
        return $this->belongsTo(Paroki::class);
    }

    public function klerus(): BelongsTo
    {
        return $this->belongsTo(Klerus::class);
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
