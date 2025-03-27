<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';
    protected $primaryKey = 'id_kecamatan';
    
    protected $fillable = [
        'kode_kecamatan',
        'nama_kecamatan',
        'id_kabupaten', // tambahkan ini untuk fillable
    ];

    // Relasi ke Kabupaten (Many-to-One)
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten', 'id_kabupaten');
    }

    // Relasi ke Desa (One-to-Many)
    public function desas()
    {
        return $this->hasMany(Desa::class, 'id_kecamatan', 'id_kecamatan');
    }

    // Relasi ke Mitra
    public function mitras()
    {
        return $this->hasMany(Mitra::class, 'id_kecamatan', 'id_kecamatan');
    }

    // Relasi ke Survei
    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_kecamatan', 'id_kecamatan');
    }
}