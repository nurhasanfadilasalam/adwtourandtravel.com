<?php

namespace App\Filament\Customer\Resources\MyGalleryResource\Pages;

use App\Filament\Customer\Resources\MyGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMyGallery extends CreateRecord
{
    protected static string $resource = MyGalleryResource::class;
}
