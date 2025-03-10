<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari nama model (provinsi -> provinsi)
    protected $table = 'provinsi';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'kode_provinsi',
        'nama_provinsi',
    ];

    // Relasi dengan model lain, misalnya jika provinsi memiliki banyak Mitra
    public function mitras()
    {
        return $this->hasMany(Mitra::class, 'id_provinsi', 'id_provinsi');
    }

    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_provinsi', 'id_provinsi');
    }
}
