<?php

namespace App\Filament\Resources;


use stdClass;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Exports\PaymentExport;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PaymentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentResource\RelationManagers;


class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = "Kelola Pembayaran";

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    // Select::make('booking_id')
                    //     ->label('Booking ID')
                    //     ->relationship('booking', 'booking_code')
                    //     ->preload()
                    //     ->reactive()
                    //     ->columnSpanFull()
                    //     ->afterStateUpdated(function ($state, callable $set) {
                    //         $paket = \App\Models\Booking::find($state);
                    //         if ($paket) {
                    //             $set('customer_id', $paket->customer_id);
                    //             $set('total_price',  (float) $paket->total_price);
                    //             $set('sisa_tagihan', (float) $paket->sisa_tagihan);
                    //             // $set('sisa_quota', $paket->kuota);
                    //             // $set('total_price', (float) $paket->harga_paket);
                    //         } else {
                    //             $set('customer_id', null);
                    //             $set('total_price', null);
                    //             $set('sisa_tagihan', null);
                    //             // $set('sisa_quota', null);
                    //             // $set('total_price', null);
                    //         }
                    //     }),

                    // Select::make('booking_id')
                    //     ->label('Booking ID')
                    //     ->relationship(
                    //         'booking',
                    //         'booking_code',
                    //         fn($query) => $query->whereNotNull('booking_code')
                    //     )
                    //     ->preload()
                    //     ->reactive()
                    //     ->columnSpanFull()
                    //     ->afterStateUpdated(function ($state, callable $set) {
                    //         $booking = \App\Models\Booking::find($state);

                    //           if ($booking && $booking->customer) {
                    //             $customer_id = $booking->customer->id; // Use the customer ID, not user_id
                    //             $set('customer_id', $customer_id);  // Set customer_id field
                    //             // Set other fields if booking exists
                    //             $set('total_price', (float) $booking->total_price);
                    //             $set('sisa_tagihan', (float) $booking->sisa_tagihan);
                    //         } else {
                    //             $set('customer_id', null);  // Clear customer_id if no booking found
                    //             $set('total_price', null);
                    //             $set('sisa_tagihan', null);
                    //         }
                    //     }),
                     Select::make('booking_id')
                        ->label('Nama Customer - Booking ID')
                        ->columnSpanFull()
                        ->relationship(
                            'booking',
                            'booking_code',
                            fn ($query) => $query->whereNotNull('booking_code')
                        )
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $booking = \App\Models\Booking::with('customer')->find($state);

                            // dd($booking);

                            $set('customer_id', $booking->customer_id);
                            $set('total_price', (float) $booking->total_price);
                            $set('sisa_tagihan', (float) $booking->sisa_tagihan);

                        }),

                    Hidden::make('customer_id')
                        ->required() // Disable editing
                        ->dehydrated(true), // Don't hydrate this field

                    TextInput::make('total_price')
                        ->label('Harga Paket')
                        ->disabled() // Disable editing
                        ->prefix('Rp.')
                        ->dehydrated(true), // Don't hydrate this field
                    TextInput::make('sisa_tagihan')
                        ->label('Sisa Tagihan')
                        ->disabled() // Disable editing
                        ->prefix('Rp.')
                        ->dehydrated(true), // Don't hydrate this field
                ])->columns(2),

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

                        // Select::make('status')
                        //     ->label("Status")
                        //     ->options([
                        //         'verified' => 'Verified',
                        //         'pending' => 'Pending',
                        //         'rejected' => 'rejected',
                        //     ])
                        //     ->default('verified')
                        //     ->required(),
                    ])->columns(2),

                ])->columns(2),

                FileUpload::make('bukti_bayar')
                    ->label('Bukti Bayar')
                    ->image() // Ensures only image files can be uploaded
                    ->disk('public') // Store files on the 'public' disk (in `storage/app/public`)
                    ->directory('payment-files') // Store images in the 'tour-leaders' folder inside the 'public' disk
                    ->maxSize(1024) // Max size in kilobytes (1MB)
                    ->enableOpen() // Allow users to open the image
                    ->default(null), // Default value for the field

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                 Tables\Columns\TextColumn::make('no')
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

                 Tables\Actions\BulkAction::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        return Excel::download(
                            new PaymentExport($livewire->getFilteredTableQuery()),
                            'payment_data.xlsx'
                        );
                    }),
                // Add a bulk action to change the status
                Tables\Actions\BulkAction::make('changeStatus')
                    ->label('Change Status to Verified') // Label of the bulk action
                    ->action(function ($records) {
                        // Loop through the selected records and update the status
                        foreach ($records as $record) {
                            // Update the status to "verified"
                            $record->update(['status' => 'verified', 'verified_by' => Auth::id(),]);
                        }

                        // Optionally, add a success notification
                        session()->flash('message', 'Selected payments have been updated to "Verified".');
                    })
                    ->icon('heroicon-o-check') // Optional: Add an icon for the action
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
