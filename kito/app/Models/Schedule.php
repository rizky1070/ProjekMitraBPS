<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $fillable = ['user_id','date_start', 'date_end', 'time_start', 'time_end', 'kegiatan', 'keterangan', 'dokumen', 'gambar'];

    public function presences(){
        return $this->hasMany(Presence::class);
    }

    public function meetingnotes(){
        return $this->hasMany(MeetingNote::class);
    }

    use HasFactory;
}
