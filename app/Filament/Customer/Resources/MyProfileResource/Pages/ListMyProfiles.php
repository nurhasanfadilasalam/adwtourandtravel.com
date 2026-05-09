<?php

namespace App\Filament\Customer\Resources\MyProfileResource\Pages;

use App\Filament\Customer\Resources\MyProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyProfiles extends ListRecords
{
    protected static string $resource = MyProfileResource::class;

    protected static string $view = 'filament.customer.resources.paket-saya-resource.pages.paket-saya';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
