<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'document_type',     // ktp, kk, passport, photo, etc.
        'document_number',   // no_ktp, no_kk, no_passport (opsional)
        'issued_at',         // tanggal dikeluarkan (passport)
        'expired_at',        // tanggal habis (passport)
        'issued_city',       // kota tempat dikeluarkan (passport)
        'file_path',         // path file dokumen
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expired_at' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
