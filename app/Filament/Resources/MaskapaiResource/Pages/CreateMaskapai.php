<?php

namespace App\Filament\Resources\MaskapaiResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MaskapaiResource;


class CreateMaskapai extends CreateRecord
{
    protected static string $resource = MaskapaiResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
