<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\PembayaranSayaResource\Pages;
use App\Filament\Customer\Resources\PembayaranSayaResource\RelationManagers;
use App\Models\Payment;
use App\Models\PembayaranSaya;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use stdClass;


class PembayaranSayaResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              Section::make([
                    Select::make('booking_id')
                        ->label('Nama Customer - Booking ID')
                        ->relationship(
                            'booking',
                            'booking_code',
                            fn ($query) => $query
                                ->whereNotNull('booking_code')
                                // ->where('create_by', auth()->id())
                            // fn ($query) => $query->with('customer')
                        )
                        ->getOptionLabelFromRecordUsing(function ($record) {
                                $customer_name = $record->customer->nama_ktp;
                                $code_booking = $record->booking_code;

                                return "{$customer_name} — {$code_booking}";
                            })
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->columnSpanFull()
                         ->afterStateUpdated(function ($state, callable $set) {
                            $booking = \App\Models\Booking::find($state);

                            if ($booking && $booking->customer) {
                                $customerid = $booking->customer->id; // Use the customer ID, not user_id

                                // dd($customerid);
                                $set('customer_id', $customerid);  // Set customer_id field
                                // Set other fields if booking exists
                                $set('total_price', (float) $booking->total_price);
                                $set('sisa_tagihan', (float) $booking->sisa_tagihan);
                            } else {
                                $set('customer_id', null);  // Clear customer_id if no booking found
                                $set('total_price', null);
                                $set('sisa_tagihan', null);
                            }
                        }),

                    TextInput::make('booking.nama_paket')
                        ->label('Paket')
                        ->readOnly(),

                    TextInput::make('total_price')
                        ->label('Harga Paket')
                        ->disabled() // Disable editing
                        ->prefix('Rp.')
                        ->dehydrated(false), // Don't hydrate this field
                    TextInput::make('sisa_tagihan')
                        ->label('Sisa Tagihan')
                        ->disabled() // Disable editing
                        ->prefix('Rp.')
                        ->dehydrated(false), // Don't hydrate this field
                ])
                ->columns(3),

                Section::make([
                    TextInput::make('jumlah_bayar')
                        ->numeric()
                        ->required()
                        ->prefix('Rp.')
                        ->rule(function (callable $get) {
                            return function ($attribute, $value, $fail) use ($get) {
                                $booking = \App\Models\Booking::find($get('booking_id'));
                                if ($booking && $value > $booking->sisa_tagihan && $booking->sisa_tagihan > 0) {
                                    $fail('Jumlah bayar melebihi sisa tagihan.');
                                }
                            };
                        })
                        ->columnSpanFull(),

                    Section::make([
                        DatePicker::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->default(now())
                            ->displayFormat('l, d M Y')
                            ->required(),

                        Select::make('metode_pembayaran')
                            ->label("Metode Pembayaran")
                            ->options([
                                'cash' => 'Cash',
                                'transfer' => 'Transfer',
                                'kartu_kredit' => 'Kartu Kredit',
                            ])
                            ->default('cash')
                            ->required(),

                    ])->columns(2),

                ])->columns(2),

                FileUpload::make('bukti_bayar')
                    ->label('Bukti Bayar')
                    ->image() // Ensures only image files can be uploaded
                    ->disk('public') // Store files on the 'public' disk (in `storage/app/public`)
                    ->visibility('public')
                    ->directory('payment-files') // Store images in the 'tour-leaders' folder inside the 'public' disk
                    ->maxSize(1024) // Max size in kilobytes (1MB)
                    ->enableOpen()
                    ->enableDownload()
                    ->default(null), // Default value for the field

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('no')
                    ->alignCenter()
                    ->state(
                        static function (HasTable $livewire, stdClass $rowLoop): string {
                            return (string) (
                                $rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
                Tables\Columns\TextColumn::make('booking.booking_code')
                    ->label('Booking Code')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_bayar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->alignCenter()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'verified' => 'success',
                        'unverified' => 'warning',
                        'rejected' => 'danger',
                    }),
                TextColumn::make('bukti_bayar')
                    ->label('Foto')
                    ->icon('heroicon-m-photo')
                    ->tooltip(function ($record) {
                        return $record->bukti_bayar !== '-'
                            ? 'Klik untuk melihat foto'
                            : 'Foto tidak tersedia';
                    })
                    ->url(function ($record) {
                        return $record->photo_url !== '-'
                            ? asset('storage/' . $record->bukti_bayar)
                            : null;
                    })
                    ->getStateUsing(function ($record) {
                        return $record->bukti_bayar !== '-'
                            ? 'Image'
                            : '';
                    }),

                TextColumn::make('verifier.name')
                    ->label('Verifier')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            'index' => Pages\ListPembayaranSayas::route('/'),
            'create' => Pages\CreatePembayaranSaya::route('/create'),
            'edit' => Pages\EditPembayaranSaya::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('created_by', Filament::auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
