<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\TourLeader;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TourLeaderResource\Pages;
use App\Filament\Resources\TourLeaderResource\RelationManagers;

class TourLeaderResource extends Resource
{
    protected static ?string $model = TourLeader::class;

    protected static ?string $navigationGroup = "Data Petugas";

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_tour_leader')
                    ->label("Nama Lengkap")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->label("NIK KTP")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_passport')
                    ->label("No Passport")
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('nomor_visa')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('tgl_awal_visa'),
                Forms\Components\DatePicker::make('tgl_akhir_visa'),
                Forms\Components\TextInput::make('id_karyawan')
                    ->label("ID Karyawan")
                    ->numeric()
                    ->default(000),
                // Forms\Components\TextInput::make('status')
                //     ->required(),
                Select::make('status')
                    ->label("Status")
                    ->options([
                    'aktif' => 'Aktif',
                    'nonaktif' => 'Tidak Aktif',
                    ])
                    ->default('aktif')
                    ->required(),
                Forms\Components\TextInput::make('nomor_handphone')
                    ->label("No Handphone")
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                FileUpload::make('photo_url')
                    ->label('Photo')
                    ->image() // Ensures only image files can be uploaded
                    ->disk('public') // Store files on the 'public' disk (in `storage/app/public`)
                    ->directory('tour-leaders') // Store images in the 'tour-leaders' folder inside the 'public' disk
                    ->maxSize(1024) // Max size in kilobytes (1MB)
                    ->enableOpen() // Allow users to open the image
                    ->default(null), // Default value for the field
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url'),
                Tables\Columns\TextColumn::make('nama_tour_leader')
                    ->label("NAMA")
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label("NIK KTP")
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_passport')
                    ->label("NO PASSPORT")
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_visa')
                    ->label("NO VISA")
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_awal_visa')
                    ->label("TGL AWAL VISA")
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_akhir_visa')
                    ->label("TGL AKHIR VISA")
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_karyawan')
                    ->label("ID KARYAWAN")
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label("STATUS"),
                Tables\Columns\TextColumn::make('nomor_handphone')
                    ->label("NO HP")
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label("EMAIL")
                    ->searchable(),
                // Tables\Columns\TextColumn::make('photo_url')
                //     ->label('Foto')
                //     ->icon('heroicon-m-photo')
                //     ->tooltip(function ($record) {
                //         return $record->photo_url !== '-'
                //             ? 'Klik untuk melihat foto'
                //             : 'Foto tidak tersedia';
                //     })
                //     ->url(function ($record) {
                //         return $record->photo_url !== '-'
                //             ? asset('storage/' . $record->photo_url)
                //             : null;
                //     })
                //     ->getStateUsing(function ($record) {
                //         return $record->photo_url !== '-'
                //             ? 'Image'
                //             : '';
                //     }),
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
