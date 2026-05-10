<?php

namespace App\Filament\Resources\PaketUmrohResource\Pages;

use App\Filament\Resources\PaketUmrohResource;
use Filament\Resources\Pages\CreateRecord;


class CreatePaketUmroh extends CreateRecord
{
    protected static string $resource = PaketUmrohResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
