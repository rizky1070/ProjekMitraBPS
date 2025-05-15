<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survei extends Model
{
    use HasFactory;
    public $timestamps = false; // Tambahkan ini!


    // Tentukan nama tabel jika berbeda dari nama model
    protected $table = 'survei';

    // Kolom yang bisa diisi (mass assignable)
    protected $primaryKey = 'id_survei'; // Menggunakan id_survei sebagai primary key
    
    protected $fillable = [
        'id_provinsi',
        'id_kabupaten',
        'nama_survei',
        'kro',
        'jadwal_kegiatan',
        'jadwal_berakhir_kegiatan',
        'bulan_dominan',
        'status_survei',
        'tim',
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



    public function mitraSurvei()
    {
        return $this->belongsToMany(Mitra::class, 'mitra_survei', 'id_survei', 'id_mitra'); 
    }

    // Model Survei
    public function survei()
    {
        return $this->hasOne(Survei::class, 'id_survei', 'id_survei');
    }

    public function mitra()
    {
        return $this->belongsToMany(Mitra::class, 'mitra_survei', 'id_survei', 'id_mitra');
    }

}
