<?php

namespace App\Models;

use App\Models\Paroki;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Keuskupan extends Model
{
    use HasFactory;

    protected $table = 'keuskupan';

    protected $fillable = ['nama', 'alamat', 'klerus_id'];

    public function klerus(): BelongsTo
    {
        return $this->belongsTo(Klerus::class);
    }

    public function paroki(): HasMany
    {
        return $this->hasMany(Paroki::class);
    }
}
