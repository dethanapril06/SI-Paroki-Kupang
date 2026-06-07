<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keluarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'keluarga';

    protected $fillable = [
        'kub_id',
        'kepala_keluarga_id',
        'alamat',
        'status_tempat_tinggal',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Keluarga $keluarga) {
            // Cascading soft delete to all Umat and their User accounts in the family
            $keluarga->umat()->get()->each(function ($umat) {
                if ($umat->user) {
                    $umat->user->delete();
                }
                $umat->delete();
            });
        });

        static::restoring(function (Keluarga $keluarga) {
            // Cascading restore to all soft-deleted Umat and their User accounts in the family
            $keluarga->umat()->withTrashed()->get()->each(function ($umat) {
                if ($umat->user()->withTrashed()->exists()) {
                    $umat->user()->withTrashed()->first()->restore();
                }
                $umat->restore();
            });
        });
    }

    // =========================================================================
    // Relasi
    // =========================================================================

    public function kub(): BelongsTo
    {
        return $this->belongsTo(Kub::class);
    }

    /** Kepala keluarga (seorang umat) */
    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'kepala_keluarga_id');
    }

    /** Semua anggota umat dalam keluarga ini */
    public function umat(): HasMany
    {
        return $this->hasMany(Umat::class);
    }
}
