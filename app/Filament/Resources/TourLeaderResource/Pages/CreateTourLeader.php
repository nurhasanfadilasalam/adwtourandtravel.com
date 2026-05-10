<?php

namespace App\Filament\Resources\TourLeaderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TourLeaderResource;


class CreateTourLeader extends CreateRecord
{
    protected static string $resource = TourLeaderResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
