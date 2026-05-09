<?php

namespace App\Filament\Resources\BandaraResource\Pages;

use App\Filament\Resources\BandaraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBandaras extends ListRecords
{
    protected static string $resource = BandaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
