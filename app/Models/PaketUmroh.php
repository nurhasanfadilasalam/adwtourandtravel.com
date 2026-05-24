<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketUmroh extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'durasi_hari',
        'kuota',
        'sisa_kuota', // Kolom ini di DB tidak perlu diisi manual lagi
        'harga_paket',
        'hotel_mekah_id',
        'hotel_madinah_id',
        'tanggal_start',
        'tanggal_end',
        'include',
        'exclude',
        'syarat',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'harga_paket' => 'decimal:2',
        'durasi_hari' => 'integer',
    ];

    public function hotel_mekah()
    {
        return $this->belongsTo(HotelMekah::class);
    }

      public function hotel_madinah()
    {
        return $this->belongsTo(HotelMadinah::class);
    }

    public function jadwalKeberangkatans()
    {
        return $this->hasMany(JadwalKeberangkatan::class);
    }

      public function bookings()
    {
        return $this->hasMany(Booking::class, 'paket_umroh_id');
    }

    // Helper
    public function getFormattedPriceAttribute()
    {
        return number_format($this->harga_paket, 0, ',', '.');
    }

    // public function getUsedQuotaAttribute(): int
    // {
    //     return $this->bookings()
    //         ->whereIn('status', ['partial', 'paid'])
    //         ->where('quota_reduced', true)
    //         ->count();
    // }

    public function getUsedQuotaAttribute(): int
    {
        return $this->bookings()
            ->where('status', '!=', 'canceled')
            // Hapus 'quota_reduced' jika Anda ingin perhitungan murni dari jumlah data
            ->count();
    }

    // public function getRemainingQuotaAttribute(): int
    // {
    //     return max(0, $this->kuota - $this->used_quota);
    // }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota - $this->used_quota);
    }

    /**
     * Agar sisa_kuota muncul saat model di-convert ke Array/JSON
     */
    protected $appends = ['sisa_kuota', 'used_quota'];

}
