<?php

namespace App\Filament\Staff\Resources;

use Carbon\Carbon;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\InfoDelayResource\Pages;
use App\Filament\Staff\Resources\InfoDelayResource\RelationManagers;


class InfoDelayResource extends Resource
{
    protected static ?string $model = JadwalKeberangkatan::class;

    protected static ?string $navigationGroup = "Kelola Jadwal";

    protected static ?string $label = 'Info Delay';

    protected static ?string $navigationLabel = 'Info Delay';

    protected static ?int $navigationSort = 2;
    
    protected static bool $shouldRegisterNavigation = false; // hide menu

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
                        ->afterStateUpdated(function ($state, callable $set) {
                            $paket = \App\Models\PaketUmroh::find($state);

                            if ($paket) {
                                $set('tanggal_keberangkatan', $paket->tanggal_start);
                                $set('tanggal_kembali', $paket->tanggal_end);
                                $set('quota', $paket->kuota);
                                $set('sisa_kuota', $paket->kuota);
                            } else {
                                $set('tanggal_keberangkatan', null);
                                $set('tanggal_kembali', null);
                                $set('quota', null);
                                $set('sisa_kuota', null);
                            }
                        }),
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

                    Section::make([
                        TextInput::make('quota')
                            ->required()
                            ->numeric()
                            ->disabled(fn(callable $get) => filled($get('paket_umroh_id')))
                            ->dehydrated(),
                        TextInput::make('sisa_kuota')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               
                Tables\Columns\TextColumn::make('detail_paket')
                    ->label('Paket')
                    ->columnSpan(3)
                    ->alignJustify()
                    ->html(true)
                    ->badge()
                    ->getStateUsing(function ($record) {

                        $data1 = ($record->paketUmroh->nama_paket);
                        $data2 = Carbon::parse($record->tanggal_keberangkatan)->translatedFormat('l, d F Y');
                        $data3 = ($record->jam_keberangkatan);
                        // Concatenate with <br> for line breaks between the two
                        return '' . $data1 . '<br>' . '' . $data2 . '<br>' . $data3 ;
                    }),

                Tables\Columns\TextColumn::make('detail_flight')
                    ->label('Penerbangan')
                    ->columnSpan(3)
                    ->alignJustify()
                    ->html(true)
                    ->badge()
                    ->getStateUsing(function ($record) {

                        $data1 = ($record->maskapai->nama_maskapai);
                        $data2 = ($record->bandara->nama_bandara);
                        // Concatenate with <br> for line breaks between the two
                        return '' . $data1 . '<br>' . '' . $data2 ;
                    }),


                Tables\Columns\TextColumn::make('tanggal_kembali')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_kuota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                  Tables\Columns\TextColumn::make('tourLeader.nama_tour_leader')
                    ->label('Tour Leader')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('muthawif.nama_muthawif')
                    ->label('Muthawif')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListInfoDelays::route('/'),
            'create' => Pages\CreateInfoDelay::route('/create'),
            'edit' => Pages\EditInfoDelay::route('/{record}/edit'),
        ];
    }
}
