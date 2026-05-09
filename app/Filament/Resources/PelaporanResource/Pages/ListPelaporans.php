<?php

namespace App\Filament\Resources\PelaporanResource\Pages;

use App\Filament\Resources\PelaporanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPelaporans extends ListRecords
{
    protected static string $resource = PelaporanResource::class;

    public function getTableRecordKey($record): string
    {
        return (string) $record->paket_umroh_id;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
