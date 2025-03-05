<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $table = 'suratkeluars';
    protected $fillable = ['perihal', 'jenis', 'nomor', 'kodeabjad', 'kodeangka', 'bulan', 'tahun', 'tanggal', 'file', 'nomorfull', 'namainstansi'];
    use HasFactory;
}
