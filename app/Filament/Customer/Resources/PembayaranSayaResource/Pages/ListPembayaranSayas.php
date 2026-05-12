<?php

namespace App\Filament\Customer\Resources\PembayaranSayaResource\Pages;

use App\Filament\Customer\Resources\PembayaranSayaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranSayas extends ListRecords
{
    protected static string $resource = PembayaranSayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
