<?php

namespace App\Filament\Resources\HotelMekahResource\Pages;

use App\Filament\Resources\HotelMekahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelMekahs extends ListRecords
{
    protected static string $resource = HotelMekahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
