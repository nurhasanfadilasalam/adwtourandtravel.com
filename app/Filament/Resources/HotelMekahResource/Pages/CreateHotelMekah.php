<?php

namespace App\Filament\Resources\HotelMekahResource\Pages;

use App\Filament\Resources\HotelMekahResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHotelMekah extends CreateRecord
{
    protected static string $resource = HotelMekahResource::class;

        protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
