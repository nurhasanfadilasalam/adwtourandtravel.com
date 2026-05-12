<?php

namespace App\Filament\Staff\Resources\InfoDelayResource\Pages;

use App\Filament\Staff\Resources\InfoDelayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInfoDelay extends CreateRecord
{
    protected static string $resource = InfoDelayResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
