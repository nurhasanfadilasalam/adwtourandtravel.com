<?php

namespace App\Filament\Staff\Resources;


use stdClass;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\PaymentStaffResource\Pages;
use App\Filament\Staff\Resources\PaymentStaffResource\RelationManagers;



class PaymentStaffResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = "Kelola Pembayaran";

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()
            ::where('created_by', auth()->id())
            ->count();
    }

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
                            fn($query) => $query->whereNotNull('booking_code')
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


                                 $set('nama_paket', $booking->paketUmroh->nama_paket ?? '-');
                            } else {
                                $set('customer_id', null);  // Clear customer_id if no booking found
                                $set('total_price', null);
                                $set('sisa_tagihan', null);
                            }
                        }),

                    TextInput::make('nama_paket')
                        ->label('Paket')
                        ->readOnly()
                        ->dehydrated(false),

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
                    // ->statePath('data')
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
                        'pending' => 'warning',
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
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                // Add a bulk action to change the status
                Tables\Actions\BulkAction::make('changeStatus')
                    ->label('Change Status to Verified') // Label of the bulk action
                    ->action(function ($records) {
                        // Loop through the selected records and update the status
                        foreach ($records as $record) {
                            // Update the status to "verified"
                            $record->update(['status' => 'verified']);
                            $record->update(['verified_by' => auth()->id()]);
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
            'index' => Pages\ListPaymentStaff::route('/'),
            'create' => Pages\CreatePaymentStaff::route('/create'),
            'edit' => Pages\EditPaymentStaff::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('created_by', auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
