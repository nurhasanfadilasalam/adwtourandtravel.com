<?php

namespace App\Models;

use App\Models\PaketUmroh;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bandara extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_bandara',
        'nama_bandara',
        'kota_bandara',
    ];

     public function bandara(): HasMany
    {
        return $this->hasMany(PaketUmroh::class);
    }
}
