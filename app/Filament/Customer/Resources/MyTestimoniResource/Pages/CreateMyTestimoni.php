<?php

namespace App\Filament\Customer\Resources\MyTestimoniResource\Pages;


use App\Filament\Customer\Resources\MyTestimoniResource;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;


class CreateMyTestimoni extends CreateRecord
{
    protected static string $resource = MyTestimoniResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['last_update'] = Carbon::now();
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }
}
