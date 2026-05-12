<?php

namespace App\Filament\Staff\Resources\PaymentStaffResource\Pages;

use App\Filament\Staff\Resources\PaymentStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;


class CreatePaymentStaff extends CreateRecord
{
    protected static string $resource = PaymentStaffResource::class;



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['verified_by'] = auth()->id();

        // dd($data);

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

            // kondisi input oleh staff default unverified - verified by admin
            // $booking->status = 'unverified';

            $booking->save();
        });

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
