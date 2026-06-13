<?php

namespace App\Models;

use App\Models\Keuskupan;
use App\Models\Kuasi;
use App\Models\Paroki;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Klerus extends Model
{
    use HasFactory;

    protected $table = 'klerus';

    protected $fillable = ['nama', 'jabatan', 'status_aktif'];

    public function keuskupan(): HasMany
    {
        return $this->hasMany(Keuskupan::class);
    }

    public function paroki(): HasMany
    {
        return $this->hasMany(Paroki::class);
    }

    public function kuasi(): HasMany
    {
        return $this->hasMany(Kuasi::class);
    }

    /** Kelompok kategorial yang dimoderatori oleh klerus ini */
    public function kategorial(): HasOne
    {
        return $this->hasOne(Kategorial::class, 'klerus_id');
    }

    /** Akun user pastor yang terhubung ke klerus ini */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    // Semua sakramen yang dipimpin klerus ini
    public function sakramen(): HasMany
    {
        return $this->hasMany(Sakramen::class, 'klerus_id');
    }
    
    // Baptis yang diberikan (sebagai pemberi dari Katolik)
    public function baptisYangDiberikan(): HasMany
    {
        return $this->hasMany(Baptis::class, 'klerus_id');
    }
    
    // Krisma yang dipimpin (khusus Uskup)
    public function krismaYangDipimpin(): HasMany
    {
        return $this->hasMany(Krisma::class, 'uskup_id');
    }
}
