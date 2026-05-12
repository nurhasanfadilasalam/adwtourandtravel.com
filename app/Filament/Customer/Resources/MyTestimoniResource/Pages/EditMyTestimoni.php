<?php

namespace App\Filament\Customer\Resources\MyTestimoniResource\Pages;


use Carbon\Carbon;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Customer\Resources\MyTestimoniResource;

class EditMyTestimoni extends EditRecord
{
    protected static string $resource = MyTestimoniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
        ];
    }

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['last_update'] = Carbon::now();
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }

     protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
