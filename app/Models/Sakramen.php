<?php

namespace App\Models;

use App\Models\Baptis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sakramen extends Model
{

    protected $table = 'sakramen';

    protected $fillable = [
        'umat_id',
        'jenis_sakramen',
        'tanggal_penerimaan',
        'paroki_id',
        'klerus_id',
    ];

    protected $casts = [
        'tanggal_penerimaan' => 'date',
    ];

    // ------------------------------------------------------------------
    // Enum helper
    // ------------------------------------------------------------------
    const JENIS = [
        'BAPTIS'          => 'Baptis',
        'KOMUNI_PERTAMA'  => 'Komuni Pertama',
        'KRISMA'          => 'Krisma',
        'PERNIKAHAN'      => 'Pernikahan',
        'MINYAK_SUCI'     => 'Minyak Suci',
    ];

    // ------------------------------------------------------------------
    // Relasi ke tabel utama
    // ------------------------------------------------------------------
    public function umat(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'umat_id');
    }

    public function paroki(): BelongsTo
    {
        return $this->belongsTo(Paroki::class, 'paroki_id');
    }

    public function klerus(): BelongsTo
    {
        return $this->belongsTo(Klerus::class, 'klerus_id');
    }

    // ------------------------------------------------------------------
    // Relasi ISA ke child tables (one-to-one)
    // ------------------------------------------------------------------
    public function baptis(): HasOne
    {
        return $this->hasOne(Baptis::class, 'sakramen_id');
    }

    public function komuniPertama(): HasOne
    {
        return $this->hasOne(KomuniPertama::class, 'sakramen_id');
    }

    public function krisma(): HasOne
    {
        return $this->hasOne(Krisma::class, 'sakramen_id');
    }

    public function pernikahan(): HasOne
    {
        return $this->hasOne(Pernikahan::class, 'sakramen_id');
    }

    public function minyakSuci(): HasOne
    {
        return $this->hasOne(MinyakSuci::class, 'sakramen_id');
    }

    // ------------------------------------------------------------------
    // Helper: ambil child sesuai jenis_sakramen
    // ------------------------------------------------------------------
    public function getDetailAttribute(): ?Model
    {
        return match ($this->jenis_sakramen) {
            'BAPTIS'         => $this->baptis,
            'KOMUNI_PERTAMA' => $this->komuniPertama,
            'KRISMA'         => $this->krisma,
            'PERNIKAHAN'     => $this->pernikahan,
            'MINYAK_SUCI'    => $this->minyakSuci,
            default          => null,
        };
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------
    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis_sakramen', $jenis);
    }

    public function scopeByUmat($query, string $umatId)
    {
        return $query->where('umat_id', $umatId);
    }
}