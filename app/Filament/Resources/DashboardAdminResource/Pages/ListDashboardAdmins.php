<?php

namespace App\Filament\Resources\DashboardAdminResource\Pages;

use App\Filament\Resources\DashboardAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDashboardAdmins extends ListRecords
{
    protected static string $resource = DashboardAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
