<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Testimoni extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'star_count',
        'last_update',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
