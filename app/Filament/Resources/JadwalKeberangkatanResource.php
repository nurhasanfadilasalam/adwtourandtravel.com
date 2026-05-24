<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\PaketUmroh;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\JadwalKeberangkatan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\JadwalKeberangkatanResource\Pages;

class JadwalKeberangkatanResource extends Resource
{
    protected static ?string $model = JadwalKeberangkatan::class;

    protected static ?string $navigationGroup = "Kelola Paket";

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Section::make([
                        Select::make('paket_umroh_id')
                            ->label('Pilih Paket Umroh')
                            ->relationship('paketUmroh', 'nama_paket')
                            ->preload()
                            ->searchable()
                            ->live()
                            // 🔑 FIX 1: Memuat data kuota master saat pertama kali halaman EDIT dibuka
                            ->afterStateHydrated(function ($state, callable $set) {
                                if (!$state) return;
                                $paket = PaketUmroh::find($state);
                                if ($paket) {
                                    $set('quota_paket', $paket->kuota);
                                    $set('sisa_quota_paket', $paket->sisa_kuota); // Membaca virtual attribute dinamis
                                }
                            })
                            // 🔑 FIX 2: Memperbarui data kuota master secara real-time saat paket diganti
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) {
                                    $set('tanggal_keberangkatan', null);
                                    $set('tanggal_kembali', null);
                                    $set('quota_paket', null);
                                    $set('sisa_quota_paket', null);
                                    return;
                                }
                                $paket = PaketUmroh::find($state);
                                if ($paket) {
                                    $set('tanggal_keberangkatan', $paket->tanggal_start);
                                    $set('tanggal_kembali', $paket->tanggal_end);
                                    $set('quota_paket', $paket->kuota);
                                    $set('sisa_quota_paket', $paket->sisa_kuota); // Membaca virtual attribute dinamis
                                }
                            })
                            ->required(),
                    ]),

                    Section::make('Informasi Kuota Paket')
                        ->description('Data kapasitas kuota yang tersisa pada master paket terkait.')
                        ->schema([
                            // 🔑 FIX 3: Menghubungkan content placeholder langsung ke form state internal
                            Placeholder::make('view_kuota')
                                ->label('Total Kuota Paket')
                                ->content(fn($get) => $get('quota_paket') ? $get('quota_paket') . ' Orang' : '-'),
                            
                            Placeholder::make('view_sisa')
                                ->label('Sisa Kuota Paket')
                                ->content(fn($get) => $get('sisa_quota_paket') !== null ? $get('sisa_quota_paket') . ' Orang' : '-'),
                        ])->columns(2),

                    // 🔑 FIX 4: Menyediakan input tersembunyi sebagai wadah penampung state memori form
                    Forms\Components\Hidden::make('quota_paket'),
                    Forms\Components\Hidden::make('sisa_quota_paket'),

                    Section::make([
                        // 🔑 FIX 5: Mengembalikan deklarasi ke ->relationship() agar jauh lebih hemat memory PHP
                        Select::make('tour_leader_id')
                            ->label('Tour Leader')
                            ->relationship('tourLeader', 'nama_tour_leader')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('muthawif_id')
                            ->label('Muthawif')
                            ->relationship('muthawif', 'nama_muthawif')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                    Section::make([
                        Select::make('maskapai_id')
                            ->label('Maskapai')
                            ->relationship('maskapai', 'nama_maskapai')
                            ->searchable()
                            ->preload()
                            ->default(null),

                        Select::make('bandara_id')
                            ->label('Bandara')
                            ->relationship('bandara', 'nama_bandara')
                            ->searchable()
                            ->preload()
                            ->default(null),
                    ])->columns(2),

                    Section::make([
                        DatePicker::make('tanggal_keberangkatan')
                            ->label('Tanggal Keberangkatan')
                            ->required()
                            ->disabled(fn(callable $get) => filled($get('paket_umroh_id')))
                            ->dehydrated(),

                        TimePicker::make('jam_keberangkatan')
                            ->required()
                            ->datalist([
                                '08:00', '09:00', '10:00', '10:30', '11:00', '11:30',
                                '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
                                '18:00', '19:00', '20:00', '21:00',
                            ]),
                            
                        DatePicker::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->required()
                            ->disabled(fn(callable $get) => filled($get('paket_umroh_id')))
                            ->dehydrated(),

                        Select::make('status')
                            ->label("Status")
                            ->options([
                                'draft' => 'Draft',
                                'open' => 'Open',
                                'closed' => 'Closed',
                                'full' => 'Full',
                                'canceled' => 'Canceled',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),
                ])
                ->icon('heroicon-o-calendar-days')
                ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paketUmroh.nama_paket')
                    ->label('Paket Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_keberangkatan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_keberangkatan')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('tourLeader.nama_tour_leader')
                    ->label('Tour Leader')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('muthawif.nama_muthawif')
                    ->label('Muthawif')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maskapai.nama_maskapai')
                    ->label('Maskapai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bandara.nama_bandara')
                    ->label('Bandara')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_kembali')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                // Menampilkan sisa kuota dinamis langsung dari relasi master paket umrohnya
                Tables\Columns\TextColumn::make('paketUmroh.sisa_kuota')
                    ->label('Sisa Kuota Paket')
                    ->suffix(' Orang')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'open' => 'success',
                        'closed' => 'danger',
                        'full' => 'warning',
                        'canceled' => 'danger',
                    }),
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
            'index' => Pages\ListJadwalKeberangkatans::route('/'),
            'create' => Pages\CreateJadwalKeberangkatan::route('/create'),
            'edit' => Pages\EditJadwalKeberangkatan::route('/{record}/edit'),
        ];
    }
}