<?php

namespace App\Filament\Staff\Resources;

use App\Filament\Staff\Resources\DashboardStaffResource\Pages;
use App\Filament\Staff\Resources\DashboardStaffResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Booking;

class DashboardStaffResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    public static function getLabel(): string
    {
        return 'Dashboard';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Dashboard::route('/'),
            'create' => Pages\CreateDashboardStaff::route('/create'),
            'edit' => Pages\EditDashboardStaff::route('/{record}/edit'),
        ];
    }
}
