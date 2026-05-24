<?php

namespace App\Filament\Staff\Resources;

use stdClass;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\PaketSaya;
use App\Models\PaketUmroh;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalKeberangkatan;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\BookingResource\Pages;
use App\Filament\Staff\Resources\BookingResource\RelationManagers;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;


class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = "Kelola Booking";

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return true;
    }

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
                Section::make('Paket Umroh')
                    ->collapsible()
                    ->schema([
                        Card::make([
                            Grid::make()
                                ->schema([
                                    Select::make('paket_umroh_id')
                                        ->label('Pilih Paket Umroh')
                                        ->columnSpanFull()
                                    
                                        ->options(
                                            PaketUmroh::query()
                                                ->orderBy('nama_paket')
                                                ->pluck('nama_paket', 'id')
                                        )
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if (! $state) {
                                                return;
                                            }
                                            $paket = PaketUmroh::find($state);
                                            if (! $paket) {
                                                return;
                                            }

                                            // dd($paket);

                                            // Cari jadwal keberangkatan
                                            $jadwal = JadwalKeberangkatan::query()
                                                ->where('paket_umroh_id', $state)
                                                ->whereDate('tanggal_keberangkatan', $paket->tanggal_start)
                                                ->where('sisa_kuota', '>', 0)
                                                ->first();

                                            if (! $jadwal) {
                                                Notification::make()
                                                    ->title('Jadwal tidak tersedia')
                                                    ->body('Silakan pilih paket lain')
                                                    ->danger()
                                                    ->send();

                                                $set('paket_umroh_id', null);
                                                return;
                                            }

                                            // dd($paket);

                                            // 🔑 simpan jadwal ID
                                            $set('jadwal_keberangkatan_id', $jadwal->id);
                                            $set('total_price', (float) $paket->harga_paket);


                                            // Simpan snapshot paket
                                            $set('paket_details', [
                                                'paket_umroh_id' => $paket->id,
                                                'harga_paket' => (float) $paket->harga_paket,
                                                'nama_paket' => $paket->nama_paket,
                                                'sisa_kuota' => $paket->sisa_kuota,
                                                'durasi_hari' => $paket->durasi_hari,
                                                'include'     => $paket->include,
                                                'exclude'     => $paket->exclude,
                                                'thumbnail'   => $paket->thumbnail,
                                            ]);
                                        }),

                                    Hidden::make('jadwal_keberangkatan_id')
                                        ->required()
                                        ->dehydrated(true),
                                    Hidden::make('total_price')
                                        ->required()
                                        ->dehydrated(true),
                                    Section::make([

                                        Placeholder::make('harga_paket_view')
                                            ->label('Harga Paket')
                                            ->extraAttributes([
                                                'class' => 'text-lg font-semibold text-primary-400',
                                            ])
                                            ->content(
                                                fn($get) =>
                                                $get('paket_details.harga_paket')
                                                    ? 'Rp ' . number_format($get('paket_details.harga_paket'), 0, ',', '.')
                                                    : '-'
                                            ),

                                        Placeholder::make('durasi_hari_view')
                                            ->label('Durasi Hari')
                                            ->extraAttributes([
                                                'class' => 'text-lg font-semibold text-primary-400',
                                            ])
                                            ->content(
                                                fn($get) =>
                                                $get('paket_details.durasi_hari')
                                                    ? $get('paket_details.durasi_hari') . ' Hari'
                                                    : '-'
                                            ),
                                        Placeholder::make('kuota_view')
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
                                    ])->columns(3),

                                    Textarea::make('paket_details.include')
                                        ->label('Include')
                                        ->disabled()
                                        ->columnSpanFull()
                                        ->rows(3)
                                        ->extraAttributes([
                                            'class' => 'text-lg font-semibold text-primary-400',
                                        ])
                                        ->dehydrated(true),

                                    Textarea::make('paket_details.exclude')
                                        ->label('Exclude')
                                        ->extraAttributes([
                                            'class' => 'text-lg font-semibold text-primary-400',
                                        ])
                                        ->disabled()
                                        ->columnSpanFull()
                                        ->rows(3)
                                        ->dehydrated(true),
                                ])
                                ->columns(2)
                                ->columnSpan(1),
                            Placeholder::make('thumbnail_preview')
                                // ->statePath('data')
                                ->label('Thumbnail')
                                ->extraAttributes(['class' => 'w-12 h-24'])
                                ->content(
                                    fn($get) =>
                                    $get('paket_details.thumbnail')
                                        ? view('components.thumbnail-preview', [
                                            'src' => $get('paket_details.thumbnail'),
                                        ])
                                        : '-'
                                ),
                        
                        ])
                            ->columns(2), // Bind all fields to $this->data

                    ]),

                Section::make('Data Booking')
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'nama_ktp')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $customer = Customer::find($state);

                                $set('nama_customer', $customer?->nama_ktp);
                            })
                            ->required(),

                        Placeholder::make('booking_code_preview')
                            ->label('Booking Code')
                            ->extraAttributes([
                                'class' => 'text-lg font-semibold text-primary-400',
                            ])
                            ->content(function ($get) {

                                $paketId = $get('paket_umroh_id');
                                $namaCustomer = $get('nama_customer');

                                if (! $paketId || ! $namaCustomer) {
                                    return '-';
                                }

                                $paket = PaketUmroh::find($paketId);
                                if (! $paket) {
                                    return '-';
                                }

                                // CUSTOMER PART (STRING, bukan object)
                                $customerPart = Str::upper(
                                    Str::limit(Str::slug($namaCustomer, ''), 5, '')
                                );

                                $paketPart = Str::upper(
                                    Str::limit(Str::slug($paket->nama_paket, ''), 5, '')
                                );

                                $datePart = now()->format('Ymd');

                                // preview only
                                $randomPart = str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);

                                return "ADW-{$paketPart}-{$customerPart}-{$datePart}-{$randomPart}";
                            }),
                    ])
                    ->columnSpanFull(),
                Hidden::make('booking_code')
                    ->dehydrated(true)
                    ->reactive()
                    ->afterStateHydrated(function (callable $get, callable $set) {
                        $set(
                            'booking_code',
                            static::generateBookingCode(
                                $get('paket_umroh_id'),
                                $get('nama_customer')
                            )
                        );
                    })
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $set(
                            'booking_code',
                            static::generateBookingCode(
                                $get('paket_umroh_id'),
                                $get('nama_customer')
                            )
                        );
                    }),

                Section::make('Payment')
                    ->schema([
                        TextInput::make('payment.jumlah_bayar')
                            ->label('Jumlah Bayar')
                            ->numeric()
                            ->required()
                            ->prefix('Rp.'),

                        DatePicker::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->default(now())
                            ->displayFormat('l, d M Y')
                            ->required(),


                        Select::make('payment.metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Cash',
                                'transfer' => 'Transfer',
                                'kartu_kredit' => 'Kartu Kredit',
                            ])
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }

                                $set('metode_pembayaran', $state);
                            }),
                        Hidden::make('metode_pembayaran')
                            ->required()
                            ->dehydrated(true),

                        FileUpload::make('payment.bukti_bayar')
                            ->label('Bukti Bayar')
                            ->disk('public')
                            ->directory('payment-files')
                            ->image(),
                        // ->required(),
                    ])
                    ->columns(2),

                ]);
    }


    protected function syncPaketDetails($paketId, callable $set): void
    {
        $paket = PaketUmroh::find($paketId);

        // dd($paket);

        if (! $paket) {
            return;
        }
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
                Tables\Columns\TextColumn::make('customer_data')
                    ->label('Customer')
                    ->columnSpan(2)
                    ->html(true)
                    ->badge()
                    ->icon('heroicon-o-lock-closed')
                    ->getStateUsing(function ($record) {
                        $bookingCode = $record->booking_code;
                        $namaCustomer = $record->customer->nama_ktp;

                        $data1 = $namaCustomer;
                        $data2 = $bookingCode;  // Format date
                        // Concatenate with <br> for line breaks between the two
                        return '' . $data1 . '<br>' . '' . $data2;
                    }),
                Tables\Columns\TextColumn::make('paket_info')
                    ->label('Paket Travel')
                    ->columnSpan(2)
                    ->html(true)
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $namaPaket = $record->paketUmroh->nama_paket;
                        $tglKeberangkatan = $record->jadwalKeberangkatan->tanggal_keberangkatan;

                        $data1 = $namaPaket;
                        $data2 = Carbon::parse($tglKeberangkatan)->translatedFormat('l, d M Y');  // Format date
                        // Concatenate with <br> for line breaks between the two
                        return 'Paket  : ' . $data1 . '<br>' . 'Tanggal Berangkat    : ' . $data2;
                    }),

                Tables\Columns\TextColumn::make('total_price')
                    ->badge()
                    ->color(
                        fn($state) =>
                        str_contains($state, '-') ? 'success' : 'success'
                    )
                    ->weight(FontWeight::Medium)
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('sisa_tagihan')
                    ->badge()
                    ->color(
                        fn($state) =>
                        str_contains($state, '-') ? 'warning' : 'warning'
                    )
                    ->weight(FontWeight::Medium)
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Pembayaran')
                        ->badge(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y');
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            
            ->actions([
                 Action::make('invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->action(function (Booking $record) {

                        // $payments = $record->payments()->oldest()->get();
                        
                        $payments = $record->payments()
                            ->reorder() // Menghapus urutan bawaan dari model jika ada
                            ->orderBy('tanggal_bayar', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();

                        $pdf = Pdf::loadView('reports.invoice-customer', [
                            'booking'  => $record,
                            'payments' => $payments,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'invoice-' . $record->booking_code . '.pdf'
                        );
                    })
                    ->visible(fn (Booking $record) =>
                        $record->payments()->exists()
                    ),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected static function generateBookingCode($paketId, $namaCustomer): ?string
    {
        if (! $paketId || ! $namaCustomer) {
            return null;
        }

        $paket = PaketUmroh::find($paketId);
        if (! $paket) {
            return null;
        }

        $customerPart = Str::upper(
            Str::limit(Str::slug($namaCustomer, ''), 5, '')
        );

        $paketPart = Str::upper(
            Str::limit(Str::slug($paket->nama_paket, ''), 5, '')
        );

        return 'ADW-' .
            $paketPart . '-' .
            $customerPart . '-' .
            now()->format('Ymd') . '-' .
            str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
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
