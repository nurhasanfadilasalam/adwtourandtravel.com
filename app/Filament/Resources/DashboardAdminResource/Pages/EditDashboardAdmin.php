<?php

namespace App\Filament\Resources\DashboardAdminResource\Pages;

use App\Filament\Resources\DashboardAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDashboardAdmin extends EditRecord
{
    protected static string $resource = DashboardAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
