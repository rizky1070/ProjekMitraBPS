<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'categories';


    public function ketuas(): HasMany
    {
        return $this->hasMany(Ketua::class);
    }
    
    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }
}