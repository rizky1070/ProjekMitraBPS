<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $table = 'presences';
    protected $fillable = ['schedule_id', 'user_id', 'absen', 'lokasi', 'jabatan', 'kegiatan', 'name', 'signature'];

    public function schedule(){
        return $this->belongsTo(Schedule::class);
    }
    use HasFactory;
}
