<?php

namespace App\Filament\Staff\Resources\DashboardStaffResource\Pages;

use App\Filament\Staff\Resources\DashboardStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDashboardStaff extends ListRecords
{
    protected static string $resource = DashboardStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
