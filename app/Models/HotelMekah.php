<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelMekah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_hotel',
        'bintang_hotel',
        'tarif_hotel_double_room',
        'tarif_hotel_triple_room',
        'tarif_hotel_suite_room',
        'contact_sales_hotel',
    ];

     public function hotel_mekah(): HasMany
    {
        return $this->hasMany(PaketUmroh::class);
    }
}
