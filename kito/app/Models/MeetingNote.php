<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingNote extends Model
{
    protected $table = 'meetingnotes';
    protected $fillable = ['user_id', 'schedule_id', 'notulen', 'filekelengkapan', 'catatan', 'kegiatan'];
    public function schedule(){
        return $this->belongsTo(Schedule::class);
    }
    use HasFactory;
}
