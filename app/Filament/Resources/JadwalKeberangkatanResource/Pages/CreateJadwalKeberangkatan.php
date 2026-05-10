<?php

namespace App\Filament\Resources\JadwalKeberangkatanResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\JadwalKeberangkatanResource;



class CreateJadwalKeberangkatan extends CreateRecord
{
    protected static string $resource = JadwalKeberangkatanResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
