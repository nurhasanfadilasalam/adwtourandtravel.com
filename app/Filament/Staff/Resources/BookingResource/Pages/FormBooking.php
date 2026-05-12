<?php

namespace App\Filament\Staff\Resources\BookingResource\Pages;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaketSaya;
use App\Models\PaketUmroh;
use Illuminate\Support\Str;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalKeberangkatan;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Staff\Resources\BookingResource;

class FormBooking extends Page implements HasForms
{
    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.staff.resources.booking-resource.pages.form-booking';

    // Form handling and state management
    use InteractsWithForms;

   // Properties to store form data and selected values
    public $pakets = [];
    public $payment_id;
    public $thumbnail;
    public $paketDetails;

    public ?array $data = [
        'customer_id' => null,
        'nama_paket' => null,
        'paket_id' => null,
        'tanggal_start' => null,
        'booking_id' => null,
        'payment_id' => null,
        'jadwal_keberangkatan_id' => null,
        'paket_details' => [
            // 'paket_id' => null,
            'tanggal_start' => null,
            'harga_paket' => null,
            'durasi_hari' => null,
            'include' => null,
            'exclude' => null,
            'booking_id' => null,
            'thumbnail' => null,
        ],
    ];

    // Mount method to initialize values before form is rendered
    public function mount(): void
    {
        // Fill the form with existing data or defaults
        // $this->form->fill($this->data);

        // if (auth()->check()) {
        //     $customer = auth()->user()->customer;

        //     if ($customer) {
        //         $this->data['customer_id'] = $customer->id;
        //     }
        // }

        if ($this->data['paket_id']) {
            $this->syncPaketDetails(
                $this->data['paket_id'],
                fn($key, $value) => data_set($this->data, $key, $value)
            );
        }

        $this->form->fill($this->data);
    }

    // Define the schema for the form
    public function getFormSchema(): array
    {
        return [
            Section::make('Paket Umroh')
                ->collapsible()
                ->schema([
                    Card::make([
                        Grid::make()
                            ->schema([
                                Select::make('paket_id')
                                    ->label('Pilih Paket Umroh')
                                    ->columnSpanFull()
                                    // ->options(PaketUmroh::query()->pluck('nama_paket', 'id'))
                                    ->options(fn() => PaketUmroh::pluck('nama_paket', 'id'))
                                    ->live() // Make it reactive
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Sync package details when a new paket_id is selected
                                        $this->syncPaketDetails($state, $set);

                                        // Generate booking code otomatis
                                        $bookingCode = $this->generateBookingCode(
                                            auth()->id(),
                                            $state
                                        );

                                        $set('booking_id', $bookingCode);
                                    }),


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
                                            $get('paket_details.kuota')
                                                ? $get('paket_details.kuota') . ' Orang'
                                                : '-'
                                        ),

                                ])->columns(3),

                                Textarea::make('paket_details.include')
                                    ->label('Include')
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->dehydrated(true),

                                Textarea::make('paket_details.exclude')
                                    ->label('Exclude')
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->dehydrated(true),
                            ])
                            ->columns(2)
                            ->columnSpan(1),
                        Placeholder::make('thumbnail_preview')
                            ->statePath('data')
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
                        ->statePath('data') // Bind all fields to $this->data
                        ->columns(2), // Bind all fields to $this->data

                ]),

            Section::make('Pilih Customer')
                ->collapsible()
                ->collapsed(false)
                ->schema([
                    Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'nama_ktp')
                            ->getOptionLabelFromRecordUsing(
                                fn($record) => $record->nama_ktp
                                    ?? 'Customer #' . $record->id
                            )
                            ->reactive()
                            ->required(),

                    Placeholder::make('booking_code')
                        ->label('Booking Code')
                        ->content(fn($get) => $get('booking_id') ?? '-')
                        ->extraAttributes([
                            'class' => 'text-lg font-semibold text-primary-400',
                        ]),
                ])
                ->statePath('data')
                ->columnSpanFull(),

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
                        ->required(),

                    FileUpload::make('payment.bukti_bayar')
                        ->label('Bukti Bayar')
                        ->disk('public')
                        ->directory('payment-files')
                        ->image(),
                    // ->required(),
                ])
                ->columns(2)
                ->statePath('data'),
        ];
    }

    /**
     * Synchronize Paket Details with the selected PaketUmroh.
     *
     * @param int|null $paketId
     * @param callable $set
     * @return void
     */

    protected function syncPaketDetails($paketId, callable $set): void
    {
        $paket = PaketUmroh::find($paketId);

        // dd($paket);

        if (! $paket) {
            return;
        }

        // dd($paket);

        // Cari jadwal keberangkatan
        $jadwal = JadwalKeberangkatan::query()
            ->where('paket_umroh_id', $paketId)
            ->whereDate('tanggal_keberangkatan', $paket->tanggal_start)
            ->where('sisa_quota', '>', 0)
            ->first();

        if (! $jadwal) {
            $this->addError('paket_id', 'Jadwal keberangkatan tidak tersedia');
            return;
        }

        $set('nama_paket', $paket->nama_paket);
        // $set('paket_id', $paketId);
        // $set('paket_details.paket_id', $paketId);
        $set('paket_details.tanggal_start', $paket->tanggal_start);
        $set('paket_details.kuota', $paket->kuota);
        $set('paket_details.harga_paket', $paket->harga_paket);
        $set('paket_details.durasi_hari', $paket->durasi_hari);
        $set('paket_details.include', $paket->include);
        $set('paket_details.exclude', $paket->exclude);
        $set('paket_details.thumbnail', $paket->thumbnail);

        // 🔑 simpan jadwal ID
        $set('jadwal_keberangkatan_id', $jadwal->id);
        // dd($paket->thumbnail);
    }

    public function save()
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {

            // dd($data);

            /** 1️⃣ CREATE BOOKING */
            $booking = Booking::create([
                'customer_id' => $data['data']['customer_id'],
                'paket_umroh_id' => $data['data']['paket_id'],
                'jadwal_keberangkatan_id' => $data['data']['jadwal_keberangkatan_id'],
                'booking_code' => $data['data']['booking_id'],
                'status' => 'waiting_payment',
                'total_price' => $data['data']['paket_details']['harga_paket'],
                'sisa_tagihan' => $data['data']['paket_details']['harga_paket'],
                'metode_pembayaran' => $data['data']['payment']['metode_pembayaran'],
                'created_by' => auth()->id(),
            ]);

            /** 2️⃣ CREATE PAYMENT */
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'customer_id' => $data['data']['customer_id'],
                'jumlah_bayar' => $data['data']['payment']['jumlah_bayar'],
                'tanggal_bayar' => $data['data']['tanggal_bayar'],
                'metode_pembayaran' => $data['data']['payment']['metode_pembayaran'],
                'bukti_bayar' => $data['data']['payment']['bukti_bayar'],
                'status' => 'unverified',
                'created_by' => auth()->id(),
            ]);

            /** 3️⃣ CREATE PAKET SAYA */
            PaketSaya::create([
                'customer_id' => $data['data']['customer_id'],
                'paket_id' => $data['data']['paket_id'],
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'created_by' => auth()->id(),
            ]);

            /** 4️⃣ UPDATE BOOKING */
            $sisa = max(
                0,
                $booking->sisa_tagihan - $payment->jumlah_bayar
            );

            $booking->update([
                'sisa_tagihan' => $sisa,
                'status' => $sisa === 0 ? 'paid' : 'partial',
            ]);

            /** 5️⃣ UPDATE KUOTA PAKET UMROH */
            $paketUmroh = PaketUmroh::find($data['data']['paket_id']);

            if ($paketUmroh && $paketUmroh->kuota > 0) {
                // Kurangi kuota paket umroh
                $paketUmroh->update([
                    'kuota' => $paketUmroh->kuota - 1,
                ]);
            } else {
                $this->addError('kuota', 'Kuota untuk paket ini sudah habis.');
                return;
            }
        });

        $this->getSavedNotification()->send();
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'));
    }

    /**
     * Validate that the booking_id is set and valid.
     *
     * @return void
     */
    // protected function validateBookingId()
    // {
    //     if (empty($this->booking_id)) {
    //         session()->flash('error', 'Booking ID is required');
    //         return;
    //     }
    // }

    public function getHargaFormattedAttribute(): string
    {
        return number_format($this->harga_paket, 0, ',', '.');
    }

    protected function generateBookingCode(
        ?int $customerId = null,
        ?int $paketId = null
    ): string {
        $customer = $customerId
            ? auth()->user()
            : null;

        $paket = $paketId
            ? PaketUmroh::find($paketId)
            : null;

        $customerPart = $customer
            ? Str::upper(Str::limit(Str::slug($customer->name, ''), 5, ''))
            : 'CUST';

        $paketPart = $paket
            ? Str::upper(Str::limit(Str::slug($paket->nama_paket, ''), 5, ''))
            : 'PKT';

        $datePart = now()->format('Ymd');

        do {
            $randomPart = str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);

            // Contoh hasil: ADW-PKT01-JOFUN-20251225-123
            $code = "ADW-{$paketPart}-{$customerPart}-{$datePart}-{$randomPart}";
        } while (
            Booking::where('booking_code', $code)->exists()
        );

        return $code;
    }





}
