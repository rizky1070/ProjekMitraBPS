<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izinkeluar extends Model
{
    protected $table = 'izinkeluars';
    protected $fillable = ['user_id', 'tanggalizin', 'jamizin', 'keperluan', 'status'];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
