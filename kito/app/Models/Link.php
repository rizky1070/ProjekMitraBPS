<?php

namespace App\Models;

use App\Models\User;
use App\Models\CategoryUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'category_user_id',
        'status', // Pastikan ada
        'priority', // Pastikan ada
        'user_id'
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
        'priority' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function categoryUser()
    {
        return $this->belongsTo(CategoryUser::class);
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
