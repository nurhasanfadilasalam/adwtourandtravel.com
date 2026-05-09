<?php

namespace App\Filament\Resources\PaketUmrohResource\Pages;

use App\Filament\Resources\PaketUmrohResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaketUmrohs extends ListRecords
{
    protected static string $resource = PaketUmrohResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
