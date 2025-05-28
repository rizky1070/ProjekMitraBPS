<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosisiMitra extends Model
{
    use HasFactory;

    protected $table = 'posisi_mitra'; // Nama tabel khusus untuk posisi

    protected $primaryKey = 'id_posisi_mitra'; // Primary key khusus

    protected $fillable = [
        'nama_posisi',
        'rate_honor',
    ];

    // Relasi ke MitraSurvei (one-to-many)
    public function mitraSurvei()
    {
        return $this->hasMany(MitraSurvei::class, 'id_posisi_mitra', 'id_posisi_mitra');
    }
}