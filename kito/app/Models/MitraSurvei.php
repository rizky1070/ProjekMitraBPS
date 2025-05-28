<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MitraSurvei extends Model
{
    use HasFactory;

    protected $table = 'mitra_survei';

    protected $primaryKey = 'id_mitra_survei';

    protected $fillable = [
        'id_mitra',
        'id_survei',
        'posisi_mitra',
        'catatan',
        'nilai',
        'vol',
        'id_posisi_mitra',
        'tgl_ikut_survei'
    ];

    public $timestamps = false; // Nonaktifkan fitur timestamps

    // Relasi dengan Mitra
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id_mitra');
    }

    // Relasi dengan Survei
    public function survei()
    {
        return $this->belongsTo(Survei::class, 'id_survei', 'id_survei');
    }

    // Relasi ke PosisiMitra
    public function posisiMitra()
    {
        return $this->belongsTo(PosisiMitra::class, 'id_posisi_mitra', 'id_posisi_mitra');
    }
}
