<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Muthawif extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_muthawif',
        'nik',
        'no_passport',
        'nomor_visa',
        'tgl_awal_visa',
        'tgl_akhir_visa',
        'id_karyawan',
        'status',
        'nomor_handphone',
        'email',
        'alamat',
        'photo_url',
    ];

    protected $casts = [
        'tgl_awal_visa' => 'date',
        'tgl_akhir_visa' => 'date',
    ];

    public function JadwalKeberangkatan(): HasMany
    {
        return $this->hasMany(JadwalKeberangkatan::class, 'muthawif_id');
    }
}
