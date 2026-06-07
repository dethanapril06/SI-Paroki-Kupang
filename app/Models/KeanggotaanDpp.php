<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeanggotaanDpp extends Model
{
    protected $table = 'keanggotaan_dpp';
    
    protected $fillable = [
        'id_umat',
        'jabatan',
        'bidang_tugas',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'string',
        'jabatan'      => 'string',
    ];

    // Konstanta jabatan
    const JABATAN = [
        'Ketua',
        'Wakil Ketua',
        'Sekretaris',
        'Bendahara',
        'Koordinator Bidang',
        'Anggota',
        'Lainnya',
    ];

    const STATUS = ['Aktif', 'Nonaktif'];

    // Relasi ke tabel Umat
    public function umat(): BelongsTo
    {
        return $this->belongsTo(Umat::class, 'id_umat', 'id');
    }

    // Scope: hanya anggota aktif
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', 'Aktif');
    }

    // Scope: filter per jabatan
    public function scopeJabatan($query, string $jabatan)
    {
        return $query->where('jabatan', $jabatan);
    }
}