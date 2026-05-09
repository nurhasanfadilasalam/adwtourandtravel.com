<?php

namespace App\Filament\Customer\Resources\MyProfileResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Customer\Resources\MyProfileResource;


class CreateMyProfile extends CreateRecord
{
    protected static string $resource = MyProfileResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = \Filament\Facades\Filament::auth()->id();
        $data['user_id'] = $userId;

        return $data;
    }
}
