<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'suratmasuks';
    protected $fillable = ['tglterima', 'tglsurat', 'nosurat', 'perihal', 'namainstansi', 'uraiandisposisi', 'file'];

    public function disposisi(){
        return $this->belongsToMany(Disposisi::class, 'suratmasukdisposisi', 'suratmasuk_id', 'disposisi_id');
    }
    use HasFactory;
}
