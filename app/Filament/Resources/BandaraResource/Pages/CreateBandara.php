<?php

namespace App\Filament\Resources\BandaraResource\Pages;


use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BandaraResource;

class CreateBandara extends CreateRecord
{
    protected static string $resource = BandaraResource::class;

        protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
