<?php

namespace App\Filament\Resources\PelaporanResource\Pages;

use App\Filament\Resources\PelaporanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPelaporan extends EditRecord
{
    protected static string $resource = PelaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
