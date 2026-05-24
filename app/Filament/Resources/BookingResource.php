<?php

namespace App\Filament\Resources;

use stdClass;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Booking;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingResource\RelationManagers;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationGroup = "Kelola Customer";

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make([
                    Section::make([
                       
                        Select::make('customer_id')
                            ->label('Customer')
                            // 1. Modifikasi query relasi untuk membawa data user agar efisien (Eager Loading)
                            ->relationship(
                                name: 'customer', 
                                titleAttribute: 'nama_ktp',
                                modifyQueryUsing: fn (Builder $query) => $query->with('user')
                            )
                            // 2. Gabungkan nama_ktp dan username di sini
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $namaKtp = $record->nama_ktp ?? 'Customer #' . $record->id;
                                
                                // Ambil username dari relasi user jika tersedia
                                $username = $record->user?->username; 

                                // Jika username ada, tampilkan "Nama KTP (Username)", jika tidak ada tampilkan "Nama KTP" saja
                                return $username 
                                    ? "{$namaKtp} ({$username})" 
                                    : $namaKtp;
                            })
                            ->searchable() // Sangat disarankan ditambahkan agar admin mudah mencari berdasarkan nama/username
                            ->preload()    // Memuat data di awal agar transisi pencarian lebih lancar
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                $set('booking_code', static::generateBookingCode(
                                    customerId: $state,
                                    paketId: $get('paket_umroh_id')
                                ))
                            )
                            ->required(),

                        TextInput::make('booking_code')
                            ->label('Booking Code')
                            ->prefix('ADW-')
                            ->readOnly()
                            ->dehydrated()
                            ->required()
                            ->default(fn() => static::generateBookingCode()),
                    ]),

                    Section::make([
                        Select::make('paket_umroh_id')
                            ->label('Pilih Paket Umroh')
                            // Tambahkan modifyQueryUsing untuk menghitung relasi bookings dengan efisien
                            ->relationship(
                                name: 'paketUmroh',
                                titleAttribute: 'nama_paket',
                                modifyQueryUsing: fn (Builder $query) => $query->withCount([
                                    // Hitung booking yang statusnya BUKAN canceled atau pending (sesuaikan kebutuhan)
                                    'bookings' => fn (Builder $query) => $query->whereNotIn('status', ['canceled', 'pending'])
                                ])
                            )
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $tanggal = $record->tanggal_start
                                    ? Carbon::parse($record->tanggal_start)->translatedFormat('d M Y')
                                    : '—';

                                // Nilai $record->bookings_count otomatis tersedia berkat withCount() di atas
                                $jumlahTerbooking = $record->bookings_count ?? 0;

                                // Hitung sisa kuota (pastikan tidak minus dengan max)
                                $sisaKuota = max(0, $record->kuota - $jumlahTerbooking);

                                return "{$record->nama_paket} — {$tanggal} -> Sisa Kuota : {$sisaKuota} Org";
                            })
                            ->preload()
                            ->reactive()
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($state, callable $set) {
                                    if (!$state) return;
                                    $paket = \App\Models\PaketUmroh::withCount([
                                        'bookings' => fn ($query) => $query->whereNotIn('status', ['canceled', 'pending'])
                                    ])->find($state);

                                    if ($paket) {
                                        $sisa = max(0, $paket->kuota - $paket->bookings_count);
                                        $set('sisa_kuota', $sisa);
                                    }
                                })
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $paket = \App\Models\PaketUmroh::withCount([
                                            'bookings' => fn ($query) => $query->whereNotIn('status', ['canceled', 'pending'])
                                        ])->find($state);
                                    if ($paket) {
                                        $sisa = max(0, $paket->kuota - $paket->bookings_count);
                                        $set('tanggal_keberangkatan', $paket->tanggal_start);
                                        $set('tanggal_kembali', $paket->tanggal_end);
                                        $set('quota', $paket->kuota);
                                        $set('sisa_kuota', $sisa);
                                        $set('total_price', (float) $paket->harga_paket);
                                    }
                                }),


                        TextInput::make('sisa_kuota')
                            ->label('Sisa Kuota Saat Ini')
                            ->readOnly()
                            ->suffix('Orang')
                            ->numeric()
                            ->helperText('Kuota tersedia berdasarkan jumlah pendaftar yang valid.'),

                        TextInput::make('total_price')
                            ->label("Biaya")
                            ->readOnly()
                            ->dehydrated(true) // send to DB
                            ->prefix('Rp.'),


                        Select::make('jadwal_keberangkatan_id')
                            ->label('Jadwal Keberangkatan')
                            ->options(
                                fn() => \App\Models\JadwalKeberangkatan::query()
                                    ->orderBy('tanggal_keberangkatan')
                                    ->get()
                                    ->mapWithKeys(fn($c) => [
                                        $c->id => Carbon::parse($c->tanggal_keberangkatan)
                                            ->translatedFormat('l, d M Y'),
                                    ])
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                    ])
                        ->columns(3),
                ])->aside(false),

                 Section::make('Payment')
                    ->schema([
                        TextInput::make('payment_jumlah_bayar')
                            ->label('Jumlah Bayar')
                            ->numeric()
                            ->required()
                            ->prefix('Rp.')
                            ->afterStateHydrated(function ($record, $set) {
                                if ($record) {
                                    $firstPayment = $record->payments()->first();
                                    $set('payment_jumlah_bayar', $firstPayment?->jumlah_bayar);
                                }
                            }),

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
                            ->visibility('public')
                            ->directory('payment-files')
                            ->image()
                            ->enableDownload()
                            ->afterStateHydrated(function ($record, $set) {
                                if ($record) {
                                    $firstPayment = $record->payments()->first();
                                    $set('payment_bukti_bayar', $firstPayment?->bukti_bayar);
                                }
                            })
                            ->dehydrated(false),
                    ])
                    ->columns(2),

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
                Tables\Columns\TextColumn::make('customer.nama_ktp')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paketUmroh.nama_paket')
                    ->label('Paket Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jadwalKeberangkatan.tanggal_keberangkatan')
                    ->label('Jadwal')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->alignCenter()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'pending' => 'danger',
                        'waiting_payment' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Harga Paket')
                    ->prefix('Rp. ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_tagihan')
                    ->prefix('Rp. ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembyaran')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
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
                Action::make('invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->visible(fn(Booking $record) => $record->payments()->exists())
                    ->disabled(function (Booking $record) {
                        return $record->payments()->where('status', '!=', 'verified')->exists();
                    })
                    ->tooltip(function (Booking $record) {
                        $adaPending = $record->payments()->where('status', '!=', 'verified')->exists();
                        return $adaPending ? 'Ada pembayaran belum disetujui.' : 'Cetak Invoice';
                    })
                    ->action(function (Booking $record) {
                        $payments = $record->payments()->reorder()->orderBy('id', 'asc')->get();
                        $pdf = Pdf::loadView('reports.invoice-customer', [
                            'booking'  => $record,
                            'payments' => $payments,
                        ]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'invoice-' . $record->booking_code . '.pdf'
                        );
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function syncPaketData(?int $paketId, callable $set): void
    {
        if (! $paketId) {
            return;
        }

        $paket = \App\Models\PaketUmroh::find($paketId);

        if (! $paket) {
            return;
        }

        $set('kuota', $paket->kuota);
        $set('sisa_kuota', $paket->kuota);
        $set('total_price', (float) $paket->harga_paket);
    }

    protected static function generateBookingCode(
        ?int $customerId = null,
        ?int $paketId = null
    ): string {
        $customer = $customerId
            ? \App\Models\Customer::find($customerId)
            : null;

        $paket = $paketId
            ? \App\Models\PaketUmroh::find($paketId)
            : null;

        $customerPart = $customer
            ? Str::upper(Str::limit(Str::slug($customer->nama_ktp, ''), 5, ''))
            : 'CUST';

        $datePart = now()->format('Ymd');

        do {
            $randomPart = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            // $code = "ADW-{$paket}-{$customerPart}-{$datePart}-{$randomPart}";
            $code = "ADW-{$customerPart}-{$datePart}-{$randomPart}";
        } while (
            \App\Models\Booking::where('booking_code', $code)->exists()
        );

        return $code;
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
