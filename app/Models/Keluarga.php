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

    /**
     * Urutkan anggota aktif berdasarkan prioritas hubungan keluarga untuk kepala keluarga:
     * Suami -> Istri -> Anak -> Ayah -> Ibu -> Saudara -> Lainnya
     * (jika sama, urutkan dari tanggal lahir tertua)
     */
    public function getAnggotaTerurutPrioritas()
    {
        $prioritas = [
            'Suami'   => 1,
            'Istri'   => 2,
            'Anak'    => 3,
            'Ayah'    => 4,
            'Ibu'     => 5,
            'Saudara' => 6,
            'Lainnya' => 7,
        ];

        return $this->umat()
            ->where('status_almarhum', false)
            ->where('status_keaktifan', 'aktif')
            ->get()
            ->sortBy(function ($umat) use ($prioritas) {
                return [
                    $prioritas[$umat->hubungan_keluarga] ?? 99,
                    $umat->tanggal_lahir ?? '9999-12-31',
                    $umat->id
                ];
            })->values();
    }

    /**
     * Dapatkan rekomendasi/prioritas utama kepala keluarga
     */
    public function getRekomendasiKepalaKeluarga()
    {
        return $this->getAnggotaTerurutPrioritas()->first();
    }

    /**
     * Otomatis set atau sesuaikan kepala keluarga berdasarkan prioritas Suami -> Istri -> Anak -> dst
     */
    public function autoSetKepalaKeluarga($save = true): ?int
    {
        $current = $this->kepalaKeluarga;
        $rekomendasi = $this->getRekomendasiKepalaKeluarga();

        if (!$rekomendasi) {
            if ($this->kepala_keluarga_id !== null) {
                $this->kepala_keluarga_id = null;
                if ($save && $this->exists) {
                    $this->saveQuietly();
                }
            }
            return null;
        }

        $prioritas = [
            'Suami'   => 1,
            'Istri'   => 2,
            'Anak'    => 3,
            'Ayah'    => 4,
            'Ibu'     => 5,
            'Saudara' => 6,
            'Lainnya' => 7,
        ];

        // Cek apakah kepala keluarga saat ini valid
        $isCurrentValid = $current 
            && !$current->status_almarhum 
            && $current->status_keaktifan === 'aktif' 
            && $current->keluarga_id === $this->id;

        $currentPriority = $isCurrentValid ? ($prioritas[$current->hubungan_keluarga] ?? 99) : 999;
        $recomPriority   = $prioritas[$rekomendasi->hubungan_keluarga] ?? 99;

        // Jika current tidak valid ATAU terdapat rekomendasi dengan prioritas lebih tinggi (misal: current Istri/Anak, tapi ada Suami)
        if (!$isCurrentValid || $recomPriority < $currentPriority) {
            $this->kepala_keluarga_id = $rekomendasi->id;
            if ($save && $this->exists) {
                $this->saveQuietly();
            }
            return $rekomendasi->id;
        }

        return $this->kepala_keluarga_id;
    }
}
