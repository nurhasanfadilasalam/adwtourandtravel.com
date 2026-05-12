<?php

namespace App\Filament\Staff\Resources\JadwalKeberangkatanResource\Pages;

use App\Filament\Staff\Resources\JadwalKeberangkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalKeberangkatans extends ListRecords
{
    protected static string $resource = JadwalKeberangkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
