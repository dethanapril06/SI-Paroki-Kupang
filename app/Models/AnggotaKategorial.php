<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AnggotaKategorial extends Pivot
{
    protected $table = 'anggota_kategorial';

    public $incrementing = true; // karena pakai id()

    protected $fillable = [
        'umat_id',
        'kategorial_id',
        'jabatan',
        'bidang_tugas',
        'tanggal_bergabung',
        'status',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
    ];

    // Relasi ke Umat
    public function umat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Umat::class);
    }

    // Relasi ke Kategorial
    public function kategorial(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Kategorial::class);
    }
}
