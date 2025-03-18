<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari nama model (Desa -> desa)
    protected $table = 'desa';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'kode_desa',
        'nama_desa',
    ];

    // Relasi dengan model lain, misalnya jika Desa memiliki banyak Mitra
    public function mitras()
    {
    return $this->hasMany(Mitra::class, 'id_desa', 'id_desa');
    }
    
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }

    public function surveis()
    {
        return $this->hasMany(survei::class, 'id_desa', 'id_desa');
    }
}
