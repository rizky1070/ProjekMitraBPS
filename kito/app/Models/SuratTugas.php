<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    protected $table = 'surattugas';
    protected $fillable = ['nosurat', 'fungsi', 'bulan', 'tahun', 'tanggalsurat', 'tanggalmulai', 'tanggalselesai', 'tujuan', 'nomorfull'];

    public function user(){
        return $this->belongsToMany(User::class, 'surattugasuser', 'surattugas_id', 'user_id');
    }
    use HasFactory;
}
