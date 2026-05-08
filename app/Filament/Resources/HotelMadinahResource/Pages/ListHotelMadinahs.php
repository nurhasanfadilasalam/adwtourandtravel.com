<?php

namespace App\Filament\Resources\HotelMadinahResource\Pages;

use App\Filament\Resources\HotelMadinahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelMadinahs extends ListRecords
{
    protected static string $resource = HotelMadinahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
