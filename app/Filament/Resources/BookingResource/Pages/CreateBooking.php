<?php

namespace App\Filament\Resources\BookingResource\Pages;

use Filament\Actions;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaketSaya;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BookingResource;
use Illuminate\Database\Eloquent\Model;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $booking = Booking::create([
                'customer_id' => $data['customer_id'],
                'paket_umroh_id' => $data['paket_umroh_id'],
                'jadwal_keberangkatan_id' => $data['jadwal_keberangkatan_id'],
                'booking_code' => $data['booking_code'],
                'status' => 'waiting_payment',
                'total_price' => $data['total_price'],
                'sisa_tagihan' => $data['total_price'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'created_by' => auth()->id(),
            ]);
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'customer_id' => $data['customer_id'],
                'jumlah_bayar' => $data['payment']['jumlah_bayar'],
                'tanggal_bayar' => $data['tanggal_bayar'],
                'metode_pembayaran' => $data['payment']['metode_pembayaran'],
                'bukti_bayar' => $data['payment']['bukti_bayar'] ?? null,
                'status' => 'unverified',
                'created_by' => auth()->id(),
            ]);
            PaketSaya::create([
                'customer_id' => $data['customer_id'],
                'paket_id' => $data['paket_umroh_id'],
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'created_by' => auth()->id(),
            ]);
            $sisa = max(0, $booking->total_price - $payment->jumlah_bayar);

            $booking->update([
                'sisa_tagihan' => $sisa,
                'status' => $sisa === 0 ? 'paid' : 'partial',
            ]);

            return $booking;
        });
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
