<?php

namespace App\Filament\Resources\BandaraResource\Pages;

use App\Filament\Resources\BandaraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBandara extends EditRecord
{
    protected static string $resource = BandaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
