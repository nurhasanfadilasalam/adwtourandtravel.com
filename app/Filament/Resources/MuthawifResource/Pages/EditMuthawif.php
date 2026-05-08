<?php

namespace App\Filament\Resources\MuthawifResource\Pages;

use App\Filament\Resources\MuthawifResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMuthawif extends EditRecord
{
    protected static string $resource = MuthawifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
