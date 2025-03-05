<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    protected $table = 'disposisi';
    protected $fillable = ['namadisposisi'];

    public function suratmasuk(){
        return $this->belongsToMany(SuratMasuk::class, 'suratmasukdisposisi');
    }
    use HasFactory;
}
