<?php

namespace App\Filament\Customer\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\PaketSaya;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use App\Models\DashboardCustomer;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Customer\Resources\DashboardCustomerResource\Pages;
use App\Filament\Customer\Resources\DashboardCustomerResource\RelationManagers;

class DashboardCustomerResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'customer';
    }


    protected static ?string $label = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $slug = 'dashboard-customer';

    protected static ?int $navigationSort = 1;

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
            ->emptyStateHeading('SELAMAT DATANG DI ADW TRAVEL')
            ->emptyStateDescription('Silakan lakukan booking Paket Umroh terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateActions([
                // Tables\Actions\Action::make('booking')
                //     ->label('Booking Paket Umroh')
                //     ->url(route('filament.customer.resources.bookings.index'))
                //     ->icon('heroicon-o-plus'),
            ])
            ->columns([
                    Split::make([
                
                    TextColumn::make('paket_info')
                        ->label('Paket Info')
                        ->columnSpan(3)
                        ->html(true)
                        ->badge()
                        ->color(fn ($state) =>
                            str_contains($state, '-') ? 'success' : 'success'
                        )
                        ->getStateUsing(fn(Booking $record) => sprintf(
                            '
                            <strong>Nama :</strong> %s<br><strong>Kode Booking :</strong> %s<br><strong>Paket :</strong> %s<br><strong>Dibayar :</strong> %s<br>',
                            e($record->customer->user->name),
                            e($record->booking_code),
                            e($record->paketUmroh?->nama_paket ?? '-'),
                            e($record->payments->sum('jumlah_bayar'))
                        )),


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
                        ->tooltip('Menunggu Verifikasi Admin')
                        ->color(fn ($state) =>
                            str_contains($state, 'verified') ? 'success' : 'warning'
                        ),
                    ])

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                 // Tombol hanya muncul jika sudah pernah ada payment diinput
                    ->visible(fn(Booking $record) => $record->payments()->exists())

                    // 🔑 KONDISI DISABLE: Jika ADA MINIMAL SATU payment yang statusnya BUKAN 'verified'
                    ->disabled(function (Booking $record) {
                        return $record->payments()
                            ->where('status', '!=', 'verified')
                            ->exists();
                    })

                    // 🔑 TOOLTIP DINAMIS: Menyesuaikan kondisi tombol
                    ->tooltip(function (Booking $record) {
                        $adaYangBelumVerified = $record->payments()
                            ->where('status', '!=', 'verified')
                            ->exists();

                        if ($adaYangBelumVerified) {
                            return 'Ada pembayaran yang belum diverifikasi oleh admin. Cetak Invoice Dinonaktifkan.';
                        }
                        
                        return 'Cetak Invoice';
                    })
                    ->action(function (Booking $record) {

                        $payments = $record->payments()->oldest()->get();

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
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListDashboardCustomers::route('/'),
            'create' => Pages\CreateDashboardCustomer::route('/create'),
            'edit' => Pages\EditDashboardCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $customerId = Filament::auth()->user()?->customer?->id;

        return parent::getEloquentQuery()
            ->where('customer_id', $customerId)
            ->latest();
    }



    public function mount(): void
    {
        // Tampilkan hanya sekali setelah login
        if (! session()->has('dashboard_modal_shown')) {
            session()->put('dashboard_modal_shown', true);

            $this->dispatch('open-dashboard-modal');
        }
    }

    protected function getActions(): array
    {
        return [
            Action::make('welcomeModal')
                ->modalHeading('Selamat Datang 🎉')
                ->modalDescription('Selamat datang di Dashboard Customer ADW Travel.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Mengerti')
                ->extraAttributes([
                    'x-on:open-dashboard-modal.window' => '$wire.mountAction("welcomeModal")',
                ]),
        ];
    }
}
