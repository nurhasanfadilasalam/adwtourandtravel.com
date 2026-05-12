<?php

namespace App\Filament\Staff\Resources\InfoDelayResource\Pages;

use App\Filament\Staff\Resources\InfoDelayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfoDelay extends EditRecord
{
    protected static string $resource = InfoDelayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
