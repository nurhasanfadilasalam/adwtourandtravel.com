<?php

namespace App\Filament\Staff\Resources\PaymentStaffResource\Pages;

use App\Filament\Staff\Resources\PaymentStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentStaff extends ListRecords
{
    protected static string $resource = PaymentStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
