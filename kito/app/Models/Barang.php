<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';
    protected $fillable = ['namabarang', 'deskripsi', 'gambar', 'stoktersedia'];

    public function permintaanbarang(){
        return $this->hasMany(PermintaanBarang::class);
    }

    public function inputbarang(){
        return $this->hasMany(InputBarang::class);
    }
    use HasFactory;
}
