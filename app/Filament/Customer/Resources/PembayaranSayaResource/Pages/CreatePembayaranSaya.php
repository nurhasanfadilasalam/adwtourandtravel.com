<?php

namespace App\Filament\Customer\Resources\PembayaranSayaResource\Pages;

use App\Filament\Customer\Resources\PembayaranSayaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaranSaya extends CreateRecord
{
    protected static string $resource = PembayaranSayaResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
