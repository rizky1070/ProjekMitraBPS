<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $table = 'provinsi';
    protected $primaryKey = 'id_provinsi';
    
    protected $fillable = [
        'kode_provinsi',
        'nama_provinsi',
    ];

    // Relasi ke Kabupaten (One-to-Many)
    public function kabupatens()
    {
        return $this->hasMany(Kabupaten::class, 'id_provinsi', 'id_provinsi');
    }

    // Relasi ke Mitra
    public function mitras()
    {
        return $this->hasMany(Mitra::class, 'id_provinsi', 'id_provinsi');
    }

    // Relasi ke Survei
    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_provinsi', 'id_provinsi');
    }
}