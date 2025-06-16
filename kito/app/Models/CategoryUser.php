<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryUser extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Alternatif: protected $fillable = ['name', 'user_id'];

    // Relasi ke User (pemilik kategori)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); // Spesifikkan foreign key
    }

    // Relasi ke Link (kategori memiliki banyak link)
    public function links(): HasMany
    {
        return $this->hasMany(Link::class, 'category_user_id'); // Spesifikkan foreign key
    }
}