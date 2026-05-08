<?php

namespace App\Filament\Resources\PaketUmrohResource\Pages;

use App\Filament\Resources\PaketUmrohResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaketUmroh extends EditRecord
{
    protected static string $resource = PaketUmrohResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
