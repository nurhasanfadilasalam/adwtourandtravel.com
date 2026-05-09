<?php

namespace App\Filament\Customer\Resources\MyProfileResource\Pages;

use App\Filament\Customer\Resources\MyProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyProfile extends EditRecord
{
    protected static string $resource = MyProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
