<?php

namespace App\Filament\Customer\Resources\PembayaranSayaResource\Pages;

use App\Filament\Customer\Resources\PembayaranSayaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranSaya extends EditRecord
{
    protected static string $resource = PembayaranSayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
