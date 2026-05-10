<?php

namespace App\Filament\Resources\PaymentResource\Pages;


use App\Models\Booking;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PaymentResource;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Filament::auth()->id();
        $data['verified_by'] = Filament::auth()->id();


        DB::transaction(function () use ($data) {

            $booking = Booking::lockForUpdate()->findOrFail($data['booking_id']);

            // Jika booking belum pernah dibayar
            if ($booking->sisa_tagihan == 0) {
                $booking->sisa_tagihan = $booking->total_price;
            }

            // Kurangi tagihan
            $booking->sisa_tagihan -= $data['jumlah_bayar'];

            // Jangan sampai minus
            if ($booking->sisa_tagihan < 0) {
                $booking->sisa_tagihan = 0;
            }

            // Update status booking
            $booking->status = $booking->sisa_tagihan == 0
                ? 'paid'
                : 'partial';

            $booking->save();
        });

        return $data;
    }

}
