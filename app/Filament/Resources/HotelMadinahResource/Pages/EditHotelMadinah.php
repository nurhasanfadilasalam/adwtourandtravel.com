<?php

namespace App\Filament\Resources\HotelMadinahResource\Pages;

use App\Filament\Resources\HotelMadinahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelMadinah extends EditRecord
{
    protected static string $resource = HotelMadinahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
