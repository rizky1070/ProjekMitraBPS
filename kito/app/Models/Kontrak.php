<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $table = 'kontraks';
    protected $fillable = ['tanggal', 'nosurat', 'kodesurat', 'uraian', 'fungsi', 'bulan', 'tahun', 'suratfull', 'file'];
    use HasFactory;
}
