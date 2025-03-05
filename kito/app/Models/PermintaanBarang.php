<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    protected $table = 'permintaanbarangs';
    protected $fillable = ['barang_id', 'user_id', 'status', 'stokpermintaan', 'ttdadmin', 'ttdumum', 'ttduser', 'catatan', 'orderdate', 'buktifoto'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function barang(){
        return $this->belongsTo(Barang::class);
    }
    use HasFactory;
}
