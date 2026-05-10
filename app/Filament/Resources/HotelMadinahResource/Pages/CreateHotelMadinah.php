<?php

namespace App\Filament\Resources\HotelMadinahResource\Pages;

use App\Filament\Resources\HotelMadinahResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHotelMadinah extends CreateRecord
{
    protected static string $resource = HotelMadinahResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
