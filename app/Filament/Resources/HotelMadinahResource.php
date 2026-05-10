<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelMadinahResource\Pages;
use App\Filament\Resources\HotelMadinahResource\RelationManagers;
use App\Models\HotelMadinah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelMadinahResource extends Resource
{
    protected static ?string $model = HotelMadinah::class;

    protected static ?string $navigationGroup = "Master Data";

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_hotel')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('bintang_hotel')
                    ->tel()
                    ->numeric()
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('tarif_hotel_double_room')
                    ->tel()
                     ->numeric()
                    ->maxLength(200)
                    ->default(null),
                Forms\Components\TextInput::make('tarif_hotel_triple_room')
                    ->tel()
                     ->numeric()
                    ->maxLength(200)
                    ->default(null),
                Forms\Components\TextInput::make('tarif_hotel_suite_room')
                    ->tel()
                     ->numeric()
                    ->maxLength(200)
                    ->default(null),
                Forms\Components\TextInput::make('contact_sales_hotel')
                    ->tel()
                    ->maxLength(200)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_hotel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bintang_hotel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tarif_hotel_double_room')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tarif_hotel_triple_room')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tarif_hotel_suite_room')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_sales_hotel')
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
            'index' => Pages\ListHotelMadinahs::route('/'),
            'create' => Pages\CreateHotelMadinah::route('/create'),
            'edit' => Pages\EditHotelMadinah::route('/{record}/edit'),
        ];
    }
}
