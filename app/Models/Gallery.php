<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'link_gambar',
        'deskripsi',
        'upload_by'
    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
