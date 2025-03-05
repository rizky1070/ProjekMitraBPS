<?php

namespace App\Models;

use App\Models\User;
use App\Models\CategoryUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Link extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function category()
    {
        return $this->belongsTo(CategoryUser::class, 'category_user_id', 'id');
    }
    public function status()
    {
        return $this->belongsTo(status::class, 'status', 'id');
    }
}
