<?php

namespace App\Filament\Customer\Resources\PaketSayaResource\Pages;

use Filament\Actions;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Customer\Resources\PaketSayaResource;


class CreatePaketSaya extends CreateRecord
{
    protected static string $resource = PaketSayaResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
