<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InfoKeberangkatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_keberangkatan_id',
        'tanggal_awal',
        'jam_awal',
        'tanggal_baru',
        'jam_baru',
        'alasan',
        'keterangan',
        'status',
    ];


    public function paketUmroh()
    {
        return $this->belongsTo(PaketUmroh::class, 'paket_umroh_id');
    }

    public function jadwalKeberangkatan()
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'jadwal_keberangkatan_id');
    }


       public function getAvailableQuotaAttribute()
    {
        return $this->quota - $this->bookings()
            ->whereNotIn('status', ['canceled', 'draft'])
            ->count();
    }

}
