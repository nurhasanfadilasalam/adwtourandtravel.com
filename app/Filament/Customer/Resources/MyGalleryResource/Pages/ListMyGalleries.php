<?php

namespace App\Filament\Customer\Resources\MyGalleryResource\Pages;

use App\Filament\Customer\Resources\MyGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyGalleries extends ListRecords
{
    protected static string $resource = MyGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
