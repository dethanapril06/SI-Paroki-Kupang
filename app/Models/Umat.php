<?php

namespace App\Models;

use App\Models\Pernikahan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Umat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'umat';

    protected $fillable = [
        'keluarga_id',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'hubungan_keluarga',
        'nama_ayah',
        'nama_ibu',
        'status_pernikahan',
        'no_telepon',
        'golongan_darah',
        'pendidikan',
        'pekerjaan',
        'penyandang_disabilitas',
        'status_almarhum',
        'status_keaktifan',
        'keterangan_lain',
    ];

    protected $casts = [
        'tanggal_lahir'         => 'date',
        'penyandang_disabilitas' => 'boolean',
        'status_almarhum'       => 'boolean',
        'keterangan_lain'       => 'boolean',
    ];

    protected static function booted()
    {
        static::created(function ($umat) {
            if ($umat->keluarga) {
                $umat->keluarga->autoSetKepalaKeluarga(true);
            }
        });

        static::updated(function ($umat) {
            // Jika umat menjadi almarhum atau tidak aktif, otomatis set status di anggota_kategorial menjadi Tidak Aktif
            if ($umat->status_almarhum || $umat->status_keaktifan !== 'aktif') {
                AnggotaKategorial::where('umat_id', $umat->id)
                    ->where('status', 'Aktif')
                    ->update(['status' => 'Tidak Aktif']);
            }

            // Otomatis sesuaikan kepala keluarga jika terjadi perubahan hubungan, keaktifan, atau pindah keluarga
            if ($umat->isDirty(['keluarga_id', 'hubungan_keluarga', 'status_almarhum', 'status_keaktifan'])) {
                if ($umat->keluarga) {
                    $umat->keluarga->autoSetKepalaKeluarga(true);
                }
                // Jika berpindah keluarga, cek juga keluarga lama
                $oldKeluargaId = $umat->getOriginal('keluarga_id');
                if ($oldKeluargaId && $oldKeluargaId != $umat->keluarga_id) {
                    $oldKeluarga = Keluarga::find($oldKeluargaId);
                    if ($oldKeluarga) {
                        $oldKeluarga->autoSetKepalaKeluarga(true);
                    }
                }
            }
        });

        static::deleted(function ($umat) {
            if ($umat->keluarga) {
                $umat->keluarga->autoSetKepalaKeluarga(true);
            }
        });
    }

    // =========================================================================
    // Relasi ke atas (parent)
    // =========================================================================

    public function scopeAktif($query)
    {
        return $query->where('status_keaktifan', 'aktif');
    }

    public function keluarga(): BelongsTo
    {
        return $this->belongsTo(Keluarga::class);
    }

    // =========================================================================
    // Relasi ke akun login
    // =========================================================================

    /** Akun user yang terhubung ke umat ini (jika ada) */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    // =========================================================================
    // Relasi sebagai ketua
    // =========================================================================

    /** Wilayah yang diketuai oleh umat ini */
    public function wilayahDiketuai(): HasMany
    {
        return $this->hasMany(Wilayah::class, 'ketua_umat_id');
    }

    /** KUB yang diketuai oleh umat ini */
    public function kubDiketuai(): HasMany
    {
        return $this->hasMany(Kub::class, 'ketua_umat_id');
    }

    /** Kategorial yang diketuai oleh umat ini */
    public function kategorialDiketuai(): HasMany
    {
        return $this->hasMany(Kategorial::class, 'ketua_umat_id');
    }

    /** Semua kategorial yang diikuti umat ini (dengan jabatan, bidang_tugas, status) */
    public function kategorial(): BelongsToMany
    {
        return $this->belongsToMany(Kategorial::class, 'anggota_kategorial')
            ->using(AnggotaKategorial::class)
            ->withPivot(['id', 'jabatan', 'bidang_tugas', 'status'])
            ->withTimestamps();
    }

    // Semua sakramen yang diterima umat ini
    public function sakramen(): HasMany
    {
        return $this->hasMany(Sakramen::class, 'umat_id');
    }
    
    // Shortcut per jenis sakramen (sekali terima)
    public function baptis(): HasOne
    {
        return $this->hasOne(Sakramen::class, 'umat_id')
                    ->where('jenis_sakramen', 'BAPTIS')
                    ->with('baptis');
    }
    
    public function komuniPertama(): HasOne
    {
        return $this->hasOne(Sakramen::class, 'umat_id')
                    ->where('jenis_sakramen', 'KOMUNI_PERTAMA')
                    ->with('komuniPertama');
    }
    
    public function krisma(): HasOne
    {
        return $this->hasOne(Sakramen::class, 'umat_id')
                    ->where('jenis_sakramen', 'KRISMA')
                    ->with('krisma');
    }
    
    public function pernikahan(): HasOne
    {
        return $this->hasOne(Sakramen::class, 'umat_id')
                    ->where('jenis_sakramen', 'PERNIKAHAN')
                    ->with('pernikahan');
    }
    
    // Minyak suci bisa diterima lebih dari sekali → HasMany
    public function minyakSuci(): HasMany
    {
        return $this->hasMany(Sakramen::class, 'umat_id')
                    ->where('jenis_sakramen', 'MINYAK_SUCI')
                    ->with('minyakSuci');
    }
    
    // Hitung berapa kali menerima minyak suci
    public function jumlahMinyakSuci(): int
    {
        return $this->minyakSuci()->count();
    }
    
    // Sebagai bapak/ibu baptis orang lain
    public function sebagaiBapakBaptis(): HasMany
    {
        return $this->hasMany(Baptis::class, 'bapak_baptis_id');
    }
    
    public function sebagaiIbuBaptis(): HasMany
    {
        return $this->hasMany(Baptis::class, 'ibu_baptis_id');
    }

    // Sebagai mempelai (dari sisi pria atau wanita)
    public function pernikahanSebagaiPria(): HasMany
    {
        return $this->hasMany(Pernikahan::class, 'mempelai_pria_id');
    }
    
    public function pernikahanSebagaiWanita(): HasMany
    {
        return $this->hasMany(Pernikahan::class, 'mempelai_wanita_id');
    }

    public function kematian(): HasOne
    {
        return $this->hasOne(Kematian::class, 'umat_id');
    }
}
