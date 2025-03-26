<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $table = 'kabupaten';
    protected $primaryKey = 'id_kabupaten';
    
    protected $fillable = [
        'kode_kabupaten',
        'nama_kabupaten',
        'id_provinsi', // tambahkan ini untuk fillable
    ];

    // Relasi ke Provinsi (Many-to-One)
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    // Relasi ke Kecamatan (One-to-Many)
    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class, 'id_kabupaten', 'id_kabupaten');
    }

    // Relasi ke Mitra
    public function mitras()
    {
        return $this->hasMany(Mitra::class, 'id_kabupaten', 'id_kabupaten');
    }

    // Relasi ke Survei
    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_kabupaten', 'id_kabupaten');
    }
}