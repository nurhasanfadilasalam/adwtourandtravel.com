<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TourLeaderResource\Pages;
use App\Filament\Resources\TourLeaderResource\RelationManagers;
use App\Models\TourLeader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TourLeaderResource extends Resource
{
    protected static ?string $model = TourLeader::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_tour_leader')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_passport')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('nomor_visa')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('tgl_awal_visa'),
                Forms\Components\DatePicker::make('tgl_akhir_visa'),
                Forms\Components\TextInput::make('id_karyawan')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('nomor_handphone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('photo_url')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_tour_leader')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_passport')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_visa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_awal_visa')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_akhir_visa')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_karyawan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('nomor_handphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo_url')
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
            'index' => Pages\ListTourLeaders::route('/'),
            'create' => Pages\CreateTourLeader::route('/create'),
            'edit' => Pages\EditTourLeader::route('/{record}/edit'),
        ];
    }
}
