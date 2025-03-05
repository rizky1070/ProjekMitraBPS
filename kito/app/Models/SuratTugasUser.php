<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugasUser extends Model
{
    protected $table = 'surattugasuser';
    protected $fillable = ['surattugas_id', 'user_id'];
    use HasFactory;
}
