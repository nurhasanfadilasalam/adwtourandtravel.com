<?php

namespace App\Filament\Customer\Resources\DashboardCustomerResource\Pages;

use App\Filament\Customer\Resources\DashboardCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDashboardCustomers extends ListRecords
{
    protected static string $resource = DashboardCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
