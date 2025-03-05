<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputBarang extends Model
{
    protected $table = 'inputbarangs';
    protected $fillable = ['barang_id', 'tanggal', 'jumlahtambah'];
    use HasFactory;

    public function barang(){
        return $this->belongsTo(Barang::class);
    }
}
