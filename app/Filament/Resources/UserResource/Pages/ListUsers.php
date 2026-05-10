<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
// use Illuminate\Database\Schema\Builder;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'staff' => Tab::make('Staff')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('role', 'staff')
                ),

            'customer' => Tab::make('Customer')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('role', 'customer')
                ),
        ];
    }
    
}
