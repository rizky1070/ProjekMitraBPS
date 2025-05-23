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
    'user_id'
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function categoryUser()
    {
        return $this->belongsTo(CategoryUser::class);
    }
}
