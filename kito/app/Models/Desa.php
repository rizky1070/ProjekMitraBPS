<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    protected $table = 'desa';
    protected $primaryKey = 'id_desa';
    
    protected $fillable = [
        'kode_desa',
        'nama_desa',
        'id_kecamatan', // tambahkan ini untuk fillable
    ];

    // Relasi ke Kecamatan (Many-to-One)
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }

    // Relasi ke Mitra
    public function mitras()
    {
        return $this->hasMany(Mitra::class, 'id_desa', 'id_desa');
    }

    // Relasi ke Survei
    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_desa', 'id_desa');
    }
}