<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends Model
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'no_ktp',
        'no_kk',
        'no_passport',
        'nama_ayah',
        'kota_passport',
        'tgl_dikeluarkan_passport',
        'tgl_habis_passport',
        'nama_ktp',
        'nama_passport',
        'alamat',
        'tgl_lahir',
        'jenis_kelamin',
        'tempat_lahir',
        'provinsi',
        'kota_kabupaten',
        'kewarganegaraan',
        'status_pernikahan',
        'jenis_pendidikan',
        'jenis_pekerjaan',
        'no_hp',
        'upload_ktp',
        'upload_kk',
        'upload_passport',
        'upload_photo',
    ];


    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_dikeluarkan_passport' => 'date',
        'tgl_habis_passport' => 'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }
}
