<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari nama model (kabupaten -> kabupaten)
    protected $table = 'kabupaten';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'kode_kabupaten',
        'nama_kabupaten',
    ];

    // Relasi dengan model lain, misalnya jika kabupaten memiliki banyak Mitra
    public function mitras()
    {
        return $this->hasMany(Mitra::class, 'id_kabupaten', 'id_kabupaten');
    }

    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_kabupaten', 'id_kabupaten');
    }
}
