<?php

namespace App\Filament\Customer\Resources\MyTestimoniResource\Pages;

use App\Filament\Customer\Resources\MyTestimoniResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyTestimonis extends ListRecords
{
    protected static string $resource = MyTestimoniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Testimoni'),
        ];
    }
}
