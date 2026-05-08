<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketSaya extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_id',
        'booking_id',
        'payment_id',
        'created_by',
    ];

    // public function paket()
    // {
    //     return $this->belongsTo(PaketUmroh::class);
    // }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
