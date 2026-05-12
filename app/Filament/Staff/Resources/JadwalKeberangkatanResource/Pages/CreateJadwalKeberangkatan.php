<?php

namespace App\Filament\Staff\Resources\JadwalKeberangkatanResource\Pages;

use App\Filament\Staff\Resources\JadwalKeberangkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJadwalKeberangkatan extends CreateRecord
{
    protected static string $resource = JadwalKeberangkatanResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

}
