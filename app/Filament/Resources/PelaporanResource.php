<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelaporanResource\Pages;
use App\Filament\Resources\PelaporanResource\RelationManagers;
use App\Models\Pelaporan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PelaporanResource extends Resource
{
    protected static ?string $model = Pelaporan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPelaporans::route('/'),
            'create' => Pages\CreatePelaporan::route('/create'),
            'edit' => Pages\EditPelaporan::route('/{record}/edit'),
        ];
    }
}
