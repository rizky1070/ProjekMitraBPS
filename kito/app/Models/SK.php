<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SK extends Model
{
    protected $table = 'sks';
    protected $fillable = ['bulan', 'tahun', 'nosurat', 'fungsi', 'nomorfull', 'uraian', 'file', 'tanggal', 'perihal'];
    use HasFactory;
}
