<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'verified_by',
        'jumlah_bayar',
        'tanggal_bayar',
        'metode_pembayaran',
        'bukti_bayar',
        'status',
        'created_by',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    // Payment → Booking (Wajib)
    // public function booking()
    // {
    //     return $this->belongsTo(Booking::class, 'booking_id');
    // }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }


    // RELATIONS
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Payment → User (Optional, jika pembayaran diverifikasi admin)
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

      public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors / Helpers
    |--------------------------------------------------------------------------
    */

    // Jumlah bayar dengan format rupiah/IDR
    public function getFormattedAmountAttribute()
    {
        return number_format($this->jumlah_bayar, 0, ',', '.');
    }

    // Status label (opsional)
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'   => 'Menunggu Verifikasi',
            'approved'  => 'Terverifikasi',
            'rejected'  => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }
}
