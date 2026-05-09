<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Clusters\MyAccount;
use App\Filament\Customer\Resources\PaketSayaResource\Pages;
use App\Filament\Customer\Resources\PaketSayaResource\RelationManagers;
use App\Models\Booking;
use App\Models\PaketSaya;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class PaketSayaResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?int $navigationSort = 2;

    // public static function getLabel(): string
    // {
    //     return 'Booking Paket';
    // }


    protected static ?string $slug = 'paket-sayas';

    // protected static ?string $cluster = MyAccount::class;cs

    protected static ?string $navigationLabel = 'Booking';

    /**
     * NO CREATE / EDIT FROM HERE
     */
    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'customer';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum ada Paket di Booking')
            ->emptyStateDescription('Silakan lakukan booking Paket Umroh terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateActions([
                // Tables\Actions\Action::make('booking')
                //     ->label('Booking Paket Umroh')
                //     ->url(route('filament.customer.resources.bookings.index'))
                //     ->icon('heroicon-o-plus'),
            ])
            ->columns([
                Panel::make([
                    Split::make([
                        TextColumn::make('customer.user.name')
                            ->icon('heroicon-s-user')
                            ->label('Nama Customer')
                            ->weight(FontWeight::Bold),

                        TextColumn::make('paket_info')
                            ->label('Paket Info')
                            ->columnSpan(3)
                            ->html(true)
                            ->badge()
                            ->color(
                                fn($state) =>
                                str_contains($state, '-') ? 'success' : 'success'
                            )
                            // ->getStateUsing(fn(Booking $record) => sprintf(
                            //     '<strong>Kode Booking:</strong> %s<br><strong>Paket:</strong> %s<br><strong>Biaya Umroh:</strong> %s',
                            //     e($record->booking_code),
                            //     e($record->paketUmroh?->nama_paket ?? '-'),
                            //     e($record->paketUmroh?->harga_paket ?? '-')
                            // )),
                            ->getStateUsing(fn(Booking $record) => sprintf(
                                '<strong>Kode Booking:</strong> %s<br><strong>Paket:</strong> %s<br><strong>Sudah Bayar:</strong> %s<br>',
                                e($record->booking_code),
                                e($record->paketUmroh?->nama_paket ?? '-'),
                                e($record->payments->sum('jumlah_bayar') ?? '-'),
                            )),


                        // TextColumn::make('total_dibayar')
                        //     ->label('Sudah Dibayar')
                        //     ->weight(FontWeight::Bold)
                        //     ->badge()
                        //     ->prefix('Sudah Bayar : ')
                        //     ->state(
                        //         fn(Booking $record) =>
                        //         $record->payments->sum('jumlah_bayar')
                        //     )
                        //     ->money('IDR', locale: 'id'),

                        TextColumn::make('status_payment')
                            ->label('Status')
                            ->weight(FontWeight::Bold)
                            ->badge()
                            ->prefix('Status : ')
                            ->state(function ($record) {
                                return Payment::where('booking_id', $record->id)
                                    ->latest()
                                    ->value('status') ?? 'Belum Bayar';
                            })
                            ->tooltip('Menunggu Verifikasi Admin - Cetak Invoice Disable')
                            ->tooltip(function (Booking $record) {
                                $latestPayment = $record->payments()->latest()->first();
                                if (! $latestPayment || $latestPayment->status !== 'verified') {
                                    return 'Menunggu Verifikasi Admin - Cetak Invoice Disable';
                                }
                                return null;
                            })
                            ->color(
                                fn($state) =>
                                str_contains($state, 'verified') ? 'success' : 'warning'
                            ),

                    ])
                ])
                ->collapsed(false),


            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                         Action::make('invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->visible(fn(Booking $record) => $record->payments()->exists())

                    // Kondisi 2: Tombol DISABLE (abu-abu) jika status payment terbaru bukan 'verified'
                    ->disabled(function (Booking $record) {
                        $latestPayment = $record->payments()->latest()->first();

                        // Jika tidak ada payment atau statusnya bukan 'verified', maka disabled = true
                        return ! $latestPayment || $latestPayment->status !== 'verified';
                    })
                    ->tooltip('Menunggu Verifikasi Admin - Cetak Invoice Disable')
                    ->tooltip(function (Booking $record) {
                        $latestPayment = $record->payments()->latest()->first();
                        if (! $latestPayment || $latestPayment->status !== 'verified') {
                            return 'Invoice tersedia setelah pembayaran diverifikasi admin.';
                        }
                        return null;
                    })
                    ->action(function (Booking $record) {
                        $payments = $record->payments()->latest()->get();

                        $pdf = Pdf::loadView('reports.invoice-customer', [
                            'booking'  => $record,
                            'payments' => $payments,
                        ]);

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            'invoice-' . $record->booking_code . '.pdf'
                        );
                    }),

                Action::make('bayar')
                    ->label('Bayar / Pelunasan')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->modalHeading('Input Pembayaran Baru')
                    ->form([
                        TextInput::make('booking_code')
                            ->label('Kode Booking')
                            ->default(fn(Booking $record) => $record->booking_code)
                            ->disabled(),

                        TextInput::make('sisa_tagihan_view')
                            ->label('Sisa Tagihan')
                            ->default(fn(Booking $record) => 'Rp ' . number_format($record->sisa_tagihan, 0, ',', '.'))
                            ->disabled(),

                        TextInput::make('jumlah_bayar')
                            ->label('Jumlah Bayar')
                            ->numeric()
                            ->required()
                            ->prefix('Rp.')
                            ->default(fn(Booking $record) => $record->sisa_tagihan)
                            ->maxValue(fn(Booking $record) => $record->sisa_tagihan),

                        DatePicker::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->default(now())
                            ->required(),

                        Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Cash',
                                'transfer' => 'Transfer',
                                'kartu_kredit' => 'Kartu Kredit',
                            ])
                            ->required(),

                        FileUpload::make('bukti_bayar')
                            ->label('Bukti Bayar')
                            ->disk('public')
                            ->directory('payment-files')
                            ->image(),
                    ])
                    ->action(function (array $data, Booking $record): void {
                        DB::transaction(function () use ($data, $record) {

                            /** 1️⃣ CREATE PAYMENT */
                            // Menggunakan key 'unverified' sesuai referensi code kamu
                            $payment = $record->payments()->create([
                                'booking_id'        => $record->id,
                                'customer_id'       => $record->customer_id,
                                'jumlah_bayar'      => $data['jumlah_bayar'],
                                'tanggal_bayar'     => $data['tanggal_bayar'],
                                'metode_pembayaran' => $data['metode_pembayaran'], // 'cash' / 'transfer' / 'kartu_kredit'
                                'bukti_bayar'       => $data['bukti_bayar'],
                                'status'            => 'unverified',
                                'created_by'        => Auth::id(),
                            ]);

                            /** 2️⃣ UPDATE BOOKING (Logic Sisa Tagihan) */
                            // Meniru logic dari FormPaketSaya kamu
                            $sisa = max(
                                0,
                                $record->sisa_tagihan - $data['jumlah_bayar']
                            );

                            $record->update([
                                'sisa_tagihan' => $sisa,
                                'status'       => $sisa === 0 ? 'paid' : 'partial',
                            ]);
                        });

                        // Notification::make()
                        //     ->title('Pembayaran Berhasil')
                        //     ->success()
                        //     ->body('Bukti pembayaran telah dikirim dan menunggu verifikasi.')
                        //     ->send();
                    })
                    ->visible(fn(Booking $record) => $record->sisa_tagihan > 0),

                ])
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
            'index' => Pages\ListPaketSayas::route('/'),
            'create' => Pages\FormPaketSaya::route('/create'),
            'edit' => Pages\EditPaketSaya::route('/{record}/edit'),
        ];
    }


    /**
     * CUSTOMER ONLY SEE THEIR OWN BOOKINGS
     */
    public static function getEloquentQuery(): Builder
    {
        $customerId = Filament::auth()->user()?->customer?->id;

        return parent::getEloquentQuery()
            ->where('customer_id', $customerId)
            ->latest();
    }
}
