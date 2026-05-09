<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalKeberangkatanResource\Pages;
use App\Filament\Resources\JadwalKeberangkatanResource\RelationManagers;
use App\Models\JadwalKeberangkatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JadwalKeberangkatanResource extends Resource
{
    protected static ?string $model = JadwalKeberangkatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('paket_umroh_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tour_leader_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('muthawif_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('maskapai_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('bandara_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\DatePicker::make('tanggal_keberangkatan')
                    ->required(),
                Forms\Components\TextInput::make('jam_keberangkatan'),
                Forms\Components\DatePicker::make('tanggal_kembali'),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paket_umroh_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tour_leader_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('muthawif_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maskapai_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bandara_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_keberangkatan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_keberangkatan'),
                Tables\Columns\TextColumn::make('tanggal_kembali')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListJadwalKeberangkatans::route('/'),
            'create' => Pages\CreateJadwalKeberangkatan::route('/create'),
            'edit' => Pages\EditJadwalKeberangkatan::route('/{record}/edit'),
        ];
    }
}
