<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';

    protected $primaryKey = 'id_mitra'; // Menggunakan id_survei sebagai primary key

    protected $fillable = [
        'id_kecamatan',
        'id_kabupaten',
        'id_provinsi',
        'id_desa',
        'sobat_id',
        'nama_lengkap',
        'alamat_mitra',
        'jenis_kelamin'
    ];

    // Relasi dengan Provinsi
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    // Relasi dengan Kabupaten
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten', 'id_kabupaten');
    }

    // Relasi dengan Kecamatan
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan','id_kecamatan');
    }

    // Relasi dengan Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class, 'id_desa', 'id_desa');
    }

    public function mitraSurvei()
    {
        return $this->belongsToMany(Survei::class, 'mitra_survei', 'id_mitra', 'id_survei');
    }

    // Model Survei
    public function mitra()
    {
        return $this->belongsToMany(Mitra::class, 'mitra_survei', 'id_mitra', 'id_survei');
    }

    public function survei()
    {
        return $this->belongsToMany(Survei::class, 'mitra_survei', 'id_mitra', 'id_survei');
    }
}
