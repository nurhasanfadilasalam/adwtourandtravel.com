<?php

namespace App\Filament\Customer\Resources\PaketSayaResource\Pages;

use App\Filament\Customer\Resources\PaketSayaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaketSaya extends EditRecord
{
    protected static string $resource = PaketSayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
