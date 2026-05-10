<?php

namespace App\Filament\Resources\GalleryResource\Pages;


use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\GalleryResource;
use Filament\Facades\Filament;

class CreateGallery extends CreateRecord
{
    protected static string $resource = GalleryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['upload_by'] = Filament::auth()->id(); // harusnya string name user
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

}
