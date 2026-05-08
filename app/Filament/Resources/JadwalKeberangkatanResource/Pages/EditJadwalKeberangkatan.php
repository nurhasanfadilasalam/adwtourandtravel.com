<?php

namespace App\Filament\Resources\JadwalKeberangkatanResource\Pages;

use App\Filament\Resources\JadwalKeberangkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalKeberangkatan extends EditRecord
{
    protected static string $resource = JadwalKeberangkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
