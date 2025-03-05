<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasukDisposisi extends Model
{
    protected $table = 'suratmasukdisposisi';
    protected $fillable = ['suratmasuk_id', 'disposisi_id'];
    use HasFactory;
}
