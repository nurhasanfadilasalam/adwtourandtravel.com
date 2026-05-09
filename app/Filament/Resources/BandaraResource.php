<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Bandara;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\BandaraResource\Pages;

class BandaraResource extends Resource
{
    protected static ?string $model = Bandara::class;

    protected static ?string $navigationGroup = "Master Data";

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_bandara')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('nama_bandara')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('kota_bandara')
                    ->required()
                    ->maxLength(200),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_bandara')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_bandara')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kota_bandara')
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
            'index' => Pages\ListBandaras::route('/'),
            'create' => Pages\CreateBandara::route('/create'),
            'edit' => Pages\EditBandara::route('/{record}/edit'),
        ];
    }
}
