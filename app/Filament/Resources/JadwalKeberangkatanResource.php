<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\PaketUmroh;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\JadwalKeberangkatan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JadwalKeberangkatanResource\Pages;
use App\Filament\Resources\JadwalKeberangkatanResource\RelationManagers;

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
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $paket = \App\Models\PaketUmroh::find($state);

                                // dd($paket);

                                if ($paket) {
                                    $set('tanggal_keberangkatan', $paket->tanggal_start);
                                    $set('tanggal_kembali', $paket->tanggal_end);
                                    $set('kuota', $paket->kuota);
                                    $set('sisa_kuota', $paket->sisa_kuota);
                                } else {
                                    $set('tanggal_keberangkatan', null);
                                    $set('tanggal_kembali', null);
                                    $set('kuota', null);
                                    $set('sisa_kuota', null);
                                }
                            })


                        // Select::make('paket_umroh_id')
                        //     ->label('Pilih Paket Umroh')
                        //     ->options(PaketUmroh::query()->pluck('nama_paket', 'id'))
                        //     ->live() // Make it reactive
                        // ->afterStateUpdated(function ($state, callable $set) {
                        //     // Sync package details when a new paket_id is selected
                        //     $this->syncPaketDetails($state, $set);
                        // }),
                        // ->afterStateUpdated(fn ($state, $set) => self::syncPaketDetails($state, $set))


                        // Displaying Paket Nama and its details
                        // TextInput::make('nama_paket')
                        //     ->label('Nama Paket')
                        //     ->disabled() // Disable editing
                        //     ->dehydrated(false), // Don't hydrate this field
                    ]),

                    Section::make([
                        Placeholder::make('kuota_view')
                            ->label('Kuota Tersedia')
                            ->extraAttributes([
                                'class' => 'text-lg font-semibold text-primary-400',
                            ])
                            ->content(
                                fn($get) =>
                                $get('paket_details.kuota')
                                    ? $get('paket_details.kuota') . ' Orang'
                                    : '-'
                            ),
                        Placeholder::make('sisa_kuota_view')
                            ->label('Kuota Tersedia')
                            ->extraAttributes([
                                'class' => 'text-lg font-semibold text-primary-400',
                            ])
                            ->content(
                                fn($get) =>
                                $get('paket_details.sisa_kuota')
                                    ? $get('paket_details.sisa_kuota') . ' Orang'
                                    : '-'
                            ),
                    ])->columns(2),

                    Section::make([
                        Forms\Components\Select::make('tour_leader_id')
                            ->label('Tour Leader')
                            ->relationship('tourLeader', 'nama_tour_leader')
                            ->searchable()
                            ->preload()
                            ->default(null),
                        Forms\Components\Select::make('muthawif_id')
                            ->label('Muthawif')
                            ->relationship('muthawif', 'nama_muthawif')
                            ->searchable()
                            ->preload()
                            ->default(null),
                    ])
                        // ->icon('heroicon-o-user')
                        ->columns(2),


                    Section::make([
                        Forms\Components\Select::make('maskapai_id')
                            ->label('Maskapai')
                            ->relationship('maskapai', 'nama_maskapai')
                            ->searchable()
                            ->preload()
                            ->default(null),

                        Forms\Components\Select::make('bandara_id')
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
                                '08:00',
                                '09:00',
                                '10:00',
                                '10:30',
                                '11:00',
                                '11:30',
                                '12:00',
                                '13:00',
                                '14:00',
                                '15:00',
                                '16:00',
                                '17:00',
                                '18:00',
                                '19:00',
                                '20:00',
                                '21:00',
                            ]),

                        DatePicker::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->required()
                            ->disabled(fn(callable $get) => filled($get('paket_umroh_id')))
                            ->dehydrated(),
                    ])->columns(2),

                    Section::make([
                        TextInput::make('quota')
                            ->required()
                            ->numeric()
                            ->disabled(fn(callable $get) => filled($get('paket_umroh_id')))
                            ->dehydrated(),
                        TextInput::make('sisa_quota')
                            ->required()
                            ->numeric()
                            ->disabled(fn(callable $get) => filled($get('paket_umroh_id')))
                            ->dehydrated(),
                    ])->columns(2),



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


                ])
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)


            ]);
    }



    /**
     * Synchronize Paket Details with the selected PaketUmroh.
     *
     * @param int|null $paketId
     * @param callable $set
     * @return void
     */


    protected static function syncPaketDetails($paketId, callable $set)
    {
        // Fetch PaketUmroh details based on selected paket_id
        $paket = PaketUmroh::find($paketId);

        // Set the related fields if paket is found
        if ($paket) {
            $set('nama_paket', $paket->nama_paket);
            $set('paket_details.harga_paket', $paket->harga_paket);
            $set('paket_details.durasi_hari', $paket->durasi_hari);
            $set('paket_details.include', $paket->include);
            $set('paket_details.exclude', $paket->exclude);
            $set('paket_details.tanggal_start', $paket->tanggal_start);
            $set('paket_details.tanggal_end', $paket->tanggal_end);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_keberangkatan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_keberangkatan'),
                Tables\Columns\TextColumn::make('paketUmroh.nama_paket')
                    ->label('Paket Name')
                    ->searchable()
                    ->sortable(),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bandara.nama_bandara')
                    ->label('Bandara')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('tanggal_kembali')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_quota')
                    ->numeric()
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
