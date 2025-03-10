<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';

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
        return $this->hasMany(MitraSurvei::class, 'id_survei', 'id_survei'); // Menghubungkan id_survei di Survei ke id_survei di MitraSurvei
    }
}
