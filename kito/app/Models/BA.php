<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BA extends Model
{
    protected $table = 'bas';
    protected $fillable = ['tanggal', 'nosurat', 'kodesurat', 'uraian', 'fungsi', 'bulan', 'tahun', 'suratfull', 'file'];
    use HasFactory;
}
