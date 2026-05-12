<?php

namespace App\Filament\Staff\Resources\CustomerResource\Pages;

use App\Filament\Staff\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;


class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
