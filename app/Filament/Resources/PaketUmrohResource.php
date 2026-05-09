<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaketUmrohResource\Pages;
use App\Filament\Resources\PaketUmrohResource\RelationManagers;
use App\Models\PaketUmroh;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaketUmrohResource extends Resource
{
    protected static ?string $model = PaketUmroh::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_paket')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('durasi_hari')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('kuota')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('harga_paket')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('hotel_mekah_id')
                    ->tel()
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('hotel_madinah_id')
                    ->tel()
                    ->numeric()
                    ->default(null),
                Forms\Components\DatePicker::make('tanggal_start'),
                Forms\Components\DatePicker::make('tanggal_end'),
                Forms\Components\Textarea::make('include')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('exclude')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('syarat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('thumbnail')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('durasi_hari')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_paket')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel_mekah_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel_madinah_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_end')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('thumbnail')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListPaketUmrohs::route('/'),
            'create' => Pages\CreatePaketUmroh::route('/create'),
            'edit' => Pages\EditPaketUmroh::route('/{record}/edit'),
        ];
    }
}
