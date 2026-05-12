<?php

namespace App\Filament\Customer\Resources\DashboardCustomerResource\Pages;

use App\Filament\Customer\Resources\DashboardCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDashboardCustomer extends CreateRecord
{
    protected static string $resource = DashboardCustomerResource::class;
}
