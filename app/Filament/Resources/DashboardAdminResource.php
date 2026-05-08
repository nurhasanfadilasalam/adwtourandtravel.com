<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DashboardAdminResource\Pages;
use App\Filament\Resources\DashboardAdminResource\Pages\Dashboard;
use App\Models\Booking;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;



class DashboardAdminResource extends Resource
{
    protected static ?string $model = Booking::class;

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
            'index' => Dashboard::route('/'),
            'create' => Pages\CreateDashboardAdmin::route('/create'),
            'edit' => Pages\EditDashboardAdmin::route('/{record}/edit'),
        ];
    }
}
