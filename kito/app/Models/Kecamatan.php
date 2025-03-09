<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari nama model (kecamatan -> kecamatan)
    protected $table = 'kecamatan';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'kode_kecamatan',
        'nama_kecamatan',
    ];

    // Relasi dengan model lain, misalnya jika kecamatan memiliki banyak Mitra
    public function mitras()
    {
        return $this->hasMany(Mitra::class, 'id_kecamatan', 'id_kecamatan');
    }
    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_kecamatan', 'id_kecamatan');
    }
}
