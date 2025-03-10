<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survei extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari nama model
    protected $table = 'survei';

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'id_provinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_desa',
        'nama_survei',
        'lokasi_survei',
        'kro',
        'jadwal_kegiatan',
        'status_survei'
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
