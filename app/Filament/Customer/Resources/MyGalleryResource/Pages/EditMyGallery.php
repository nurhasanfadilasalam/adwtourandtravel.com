<?php

namespace App\Filament\Customer\Resources\MyGalleryResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Customer\Resources\MyGalleryResource;


class EditMyGallery extends EditRecord
{
    protected static string $resource = MyGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['upload_by'] = auth()->user?->name();
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }


}
