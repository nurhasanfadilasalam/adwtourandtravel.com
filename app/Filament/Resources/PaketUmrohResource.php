<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\PaketUmroh;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\PaketUmrohResource\Pages;


class PaketUmrohResource extends Resource
{
    protected static ?string $model = PaketUmroh::class;

    protected static ?string $navigationGroup = "Kelola Paket";


    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_paket')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('durasi_hari')
                    ->suffix('Hari')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('kuota')
                    ->suffix('Orang')
                    ->required()
                    ->numeric()
                    ->default(0),
                //new
                Hidden::make('sisa_kuota')
                    ->required()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('harga_paket')
                    ->required()
                    ->prefix('Rp.')
                    ->numeric()
                    ->default(0.00),
                Select::make('hotel_mekah_id')
                    ->label('Hotel Mekah')
                    ->options(fn () => \App\Models\HotelMekah::all()
                        ->mapWithKeys(fn($c) => [$c->id => (string) ($c->nama_hotel ?? '—')])
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('hotel_madinah_id')
                    ->label('Hotel Madinah')
                    ->options(fn () => \App\Models\HotelMadinah::all()
                        ->mapWithKeys(fn($c) => [$c->id => (string) ($c->nama_hotel ?? '—')])
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_start')
                    ->label('Tanggal Keberangkatan'),
                Forms\Components\DatePicker::make('tanggal_end')
                    ->label('Tanggal Kepulangan'),
                Forms\Components\Textarea::make('include')
                    ->default('Visa Umroh, Tour Leader Berpengalaman, Free Sertifikat, Muthawif Tersertifikasi, Perlengkapan Umroh, Tiket Pesawat International/Domestik, Free Air Zam-Zam, Free Doc Photo Video, Hotel-Bus Full AC & Makan 3x')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('exclude')
                ->default('Passport, Vaksin Meningitis, Keperluan Pribadi')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('syarat')
                    ->columnSpanFull(),
                FileUpload::make('thumbnail')
                    ->label('Gambar Thumbnail')
                    ->image() // Ensures only image files can be uploaded
                    ->disk('public') // Store files on the 'public' disk (in `storage/app/public`)
                    ->directory('thumbnail-files') // Store images in the 'tour-leaders' folder inside the 'public' disk
                    ->maxSize(1024) // Max size in kilobytes (1MB)
                    ->enableOpen() // Allow users to open the image
                    ->columnSpanFull()
                    ->default(null), // Default value for the field
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
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
                Tables\Columns\TextColumn::make('hotel_mekah.nama_hotel')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel_madinah.nama_hotel')
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
