<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Maskapai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\MaskapaiResource\Pages;

class MaskapaiResource extends Resource
{
    protected static ?string $model = Maskapai::class;

    protected static ?string $navigationGroup = "Master Data";

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_maskapai')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode_iata')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode_icao')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('logo')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_maskapai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_iata')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_icao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListMaskapais::route('/'),
            'create' => Pages\CreateMaskapai::route('/create'),
            'edit' => Pages\EditMaskapai::route('/{record}/edit'),
        ];
    }
}
