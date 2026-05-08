<?php

namespace App\Filament\Resources\HotelMekahResource\Pages;

use App\Filament\Resources\HotelMekahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelMekah extends EditRecord
{
    protected static string $resource = HotelMekahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
