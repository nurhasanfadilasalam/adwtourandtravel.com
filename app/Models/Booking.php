<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'paket_umroh_id',
        'jadwal_keberangkatan_id',
        'booking_code',
        'status',
        'total_price',
        'sisa_tagihan',
        'metode_pembayaran',
        'created_by',
    ];


    protected $casts = [
        'total_price' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
    ];


    // RELATIONS
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function paketUmroh()
    {
        return $this->belongsTo(PaketUmroh::class, 'paket_umroh_id');
    }

    public function jadwalKeberangkatan()
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'jadwal_keberangkatan_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paket()
    {
        return $this->belongsTo(PaketUmroh::class, 'paket_umroh_id');
    }

    // BOOT: Auto-generate booking code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (! $booking->booking_code) {
                $booking->booking_code = 'ADW-'.Str::upper(Str::random(8));
            }
        });

         // after creating a booking, decrement sisa_quota automatically (optional)
        static::created(function ($booking) {
            try {
                if ($booking->jadwal_keberangkatan_id) {
                    $jadwal = $booking->jadwal;
                    if ($jadwal) {
                        $jadwal->decrement('sisa_quota', 1);
                    }
                }
            } catch (\Throwable $e) {
                // swallow to avoid breaking booking creation in edge cases
            }
        });

        // on deleting booking, increment sisa_quota if not canceled
        static::deleted(function ($booking) {
            try {
                if ($booking->jadwal_keberangkatan_id) {
                    $jadwal = $booking->jadwal;
                    if ($jadwal && $booking->status !== 'canceled') {
                        $jadwal->increment('sisa_quota', 1);
                    }
                }
            } catch (\Throwable $e) {
            }
        });
    }

    // public function getIsFullyPaidAttribute()
    // {
    //     return $this->payments()->sum('amount') >= $this->total_price;
    // }

    public function getTanggalFormattedAttribute(): string
    {
        return Carbon::parse($this->tanggal_keberangkatan)
            ->translatedFormat('l, d M Y');
    }
}
