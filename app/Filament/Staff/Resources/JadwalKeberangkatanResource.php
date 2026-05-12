<?php

namespace App\Filament\Staff\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\JadwalKeberangkatan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\JadwalKeberangkatanResource\Pages;
use App\Filament\Staff\Resources\JadwalKeberangkatanResource\RelationManagers;


class JadwalKeberangkatanResource extends Resource
{
    protected static ?string $model = JadwalKeberangkatan::class;

    protected static ?string $navigationGroup = "Kelola Jadwal";

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Select::make('paket_umroh_id')
                        ->label('Pilih Paket Umroh')
                        ->relationship('paketUmroh', 'nama_paket')
                        ->preload()
                        ->reactive()
                        ->columnSpanFull()
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
                        }),

                    Section::make([
                        Placeholder::make('kuota_view')
                            ->label('Kuota Tersedia')
                            ->extraAttributes([
                                'class' => 'text-lg font-semibold text-primary-400',
                            ])
                            ->content(
                                fn($get) =>
                                $get('paket.kuota')
                                    ? $get('paket.kuota') . ' Orang'
                                    : '-'
                            ),
                        Placeholder::make('sisa_kuota_view')
                            ->label('Kuota Tersedia')
                            ->extraAttributes([
                                'class' => 'text-lg font-semibold text-primary-400',
                            ])
                            ->content(
                                fn($get) =>
                                $get('paket.sisa_kuota')
                                    ? $get('paket.sisa_kuota') . ' Orang'
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
                            ->required()
                            ->label('Tanggal Keberangkatan')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paketUmroh.nama_paket')
                    ->label('Paket Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_keberangkatan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_keberangkatan'),
                Tables\Columns\TextColumn::make('tourLeader.nama_tour_leader')
                    ->label('Tour Leader')
                    ->sortable(),
                Tables\Columns\TextColumn::make('muthawif.nama_muthawif')
                    ->label('Muthawif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('maskapai.nama_maskapai')
                    ->label('Maskapai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bandara.nama_bandara')
                    ->label('Bandara')
                    ->sortable(),
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
