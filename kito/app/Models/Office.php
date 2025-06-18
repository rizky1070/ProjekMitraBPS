<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
    'name', 
    'link', 
    'category_id', 
    'status',
    'priority' 
    ];

    protected $guarded = ['id'];
    
    protected $casts = [
        'status' => 'boolean',
        'priority' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function getFullUrlAttribute(): string
    {
        // Ambil nilai link asli dari database
        $url = $this->attributes['link'];

        // Jika link sudah memiliki http:// atau https://, kembalikan apa adanya.
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        // Jika tidak, tambahkan '//' di depannya.
        return '//' . $url;
    }
}