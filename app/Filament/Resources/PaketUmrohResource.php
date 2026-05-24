<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaketUmrohResource\Pages;
use App\Models\PaketUmroh;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextInput::make('nama_paket')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                
                Textarea::make('deskripsi')
                    ->columnSpanFull(),
                
                TextInput::make('durasi_hari')
                    ->suffix('Hari')
                    ->required()
                    ->numeric()
                    ->default(0),

                // 🔑 FIX 1: Hapus live() dan afterStateUpdated statis sisa_kuota
                TextInput::make('kuota')
                    ->label('Total Kuota Maksimal')
                    ->suffix('Orang')
                    ->required()
                    ->numeric()
                    ->default(0),

                TextInput::make('harga_paket')
                    ->required()
                    ->prefix('Rp. ')
                    ->numeric()
                    ->default(0.00),

                // 🔑 FIX 2: Optimasi query hotel menggunakan pluck() langsung ke database
                Select::make('hotel_mekah_id')
                    ->label('Hotel Mekah')
                    ->options(fn () => \App\Models\HotelMekah::query()->pluck('nama_hotel', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('hotel_madinah_id')
                    ->label('Hotel Madinah')
                    ->options(fn () => \App\Models\HotelMadinah::query()->pluck('nama_hotel', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('tanggal_start')
                    ->label('Tanggal Keberangkatan')
                    ->required(), // Disarankan required agar jadwal keberangkatan sinkron
                
                DatePicker::make('tanggal_end')
                    ->label('Tanggal Kepulangan')
                    ->required(),

                Textarea::make('include')
                    ->default('Visa Umroh, Tour Leader Berpengalaman, Free Sertifikat, Muthawif Tersertifikasi, Perlengkapan Umroh, Tiket Pesawat International/Domestik, Free Air Zam-Zam, Free Doc Photo Video, Hotel-Bus Full AC & Makan 3x')
                    ->columnSpanFull(),
                
                Textarea::make('exclude')
                    ->default('Passport, Vaksin Meningitis, Keperluan Pribadi')
                    ->columnSpanFull(),
                
                Textarea::make('syarat')
                    ->columnSpanFull(),

                FileUpload::make('thumbnail')
                    ->label('Gambar Thumbnail')
                    ->image()
                    ->disk('public')
                    ->directory('thumbnail-files')
                    ->maxSize(1024)
                    ->enableOpen()
                    ->enableDownload()
                    ->columnSpanFull()
                    ->default(null),

                Toggle::make('is_active')
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
                    ->suffix(' Hari')
                    ->alignCenter()
                    ->sortable(),
                
                // 🔑 FIX 3: Menampilkan data kuota asal, beserta sisa kuota dinamis dari virtual attribute
                Tables\Columns\TextColumn::make('kuota')
                    ->label('Kuota (Total/Sisa)')
                    ->getStateUsing(function (PaketUmroh $record) {
                        return "{$record->kuota} / {$record->sisa_kuota}";
                    })
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('harga_paket')
                    ->money('IDR')
                    ->sortable(),
                
                // 🔑 FIX 4: Menghapus method ->numeric() yang salah tempat
                Tables\Columns\TextColumn::make('hotel_mekah.nama_hotel')
                    ->label('Hotel Mekah')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('hotel_madinah.nama_hotel')
                    ->label('Hotel Madinah')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_start')
                    ->label('Berangkat')
                    ->date('d M Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_end')
                    ->label('Pulang')
                    ->date('d M Y')
                    ->sortable(),
                
                // 🔑 FIX 5: Menampilkan pratinjau thumbnail berupa gambar di baris tabel, bukan teks path
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Foto')
                    ->disk('public'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->alignCenter()
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaketUmrohs::route('/'), 
            'create' => Pages\CreatePaketUmroh::route('/create'),
            'edit' => Pages\EditPaketUmroh::route('/{record}/edit'),
        ];
    }
}