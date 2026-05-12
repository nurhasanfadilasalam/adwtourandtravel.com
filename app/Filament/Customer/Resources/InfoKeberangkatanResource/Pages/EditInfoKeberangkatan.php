<?php

namespace App\Filament\Customer\Resources\InfoKeberangkatanResource\Pages;

use App\Filament\Customer\Resources\InfoKeberangkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfoKeberangkatan extends EditRecord
{
    protected static string $resource = InfoKeberangkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
