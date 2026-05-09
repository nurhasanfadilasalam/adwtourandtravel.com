<?php

namespace App\Filament\Resources;

use stdClass;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Booking;
use Filament\Forms\Form;
use App\Models\Pelaporan;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PelaporanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PelaporanResource\RelationManagers;
use Filament\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;

class PelaporanResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Kelola Pelaporan";

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Pelaporan';

    public static function getLabel(): string
    {
        return 'Detail Paket';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    // public static function canView($record): bool
    // {
    //     return false;
    // }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
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
                TextColumn::make('paketUmroh.nama_paket')
                    ->label('Paket Umroh')
                    ->sortable()
                    ->searchable(),


                TextColumn::make('harga_paket')
                    ->label('Biaya')
                    ->weight(FontWeight::Bold)
                    ->badge()
                    ->money('IDR', locale: 'id'),
                TextColumn::make('periode_keberangkatan')
                    ->label('Periode Umroh')
                    ->columnSpan(3)
                    ->alignCenter()
                    ->html(true)
                    ->getStateUsing(function ($record) {
                        // Format the 'tgl_dikeluarkan_passport' and 'tgl_habis_passport' dates
                        $data1 = Carbon::parse($record->tanggal_start)->locale('id')->translatedFormat('l, d M Y');
                        $data2 = Carbon::parse($record->tanggal_end)->locale('id')->translatedFormat('l, d M Y');

                        // Concatenate with <br> for line breaks between the two
                        return 'Start  : ' . $data1 . '<br>' . 'End    : ' . $data2;
                    }),

                TextColumn::make('total_booking')
                    ->label('Total Customer')
                    ->suffix(' Org')
                    ->badge()
                    ->numeric()
                    ->color('primary'),

                TextColumn::make('laki_laki')
                    ->label('Laki-laki')
                    ->suffix(' Org')
                    ->badge()
                    ->color('success'),

                TextColumn::make('perempuan')
                    ->label('Perempuan')
                    ->suffix(' Org')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('total_booking', 'desc')
            ->actionsPosition(\Filament\Tables\Enums\ActionsPosition::BeforeColumns)
            ->actions([
                Action::make('lihatCustomer')
                    ->label('Lihat Pembayaran')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->extraAttributes([
                        'class' => 'w-full mb-1',
                    ])
                    ->url(fn($record) => Pages\ListPelaporanCustomer::getUrl([
                        'paket_umroh_id' => $record->paket_umroh_id,
                        'jadwal_keberangkatan_id' => $record->jadwal_keberangkatan_id,
                    ])),

                Action::make('lihatManifest')
                    ->label('Manifest Customer')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'w-full mb-1',
                    ])
                    ->url(fn($record) => Pages\ListCustomerManifest::getUrl([
                        'paket_umroh_id' => $record->paket_umroh_id,
                        'jadwal_keberangkatan_id' => $record->jadwal_keberangkatan_id,
                    ])),
            ],position: ActionsPosition::BeforeCells)

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                /* =========================
                 * EXPORT PDF (FIXED)
                 * ========================= */
                // Tables\Actions\BulkAction::make('export_pdf')
                //     ->label('Export PDF')
                //     ->icon('heroicon-o-document-arrow-down')
                //     ->action(function ($records) {
                //         return response()->streamDownload(function () use ($records) {
                //             // dd($records);
                //             echo Pdf::loadView('reports.laporan-per-paket-report', [
                //                 'records' => $records,
                //                 // 'paket' => $paket,
                //                 // 'periode' => $periode,
                //                 // 'customers' => $customers,
                //             ])->stream();
                //         }, 'summary-per-package-report.pdf');
                //     }),
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
            'index' => Pages\ListPelaporans::route('/'),
            'create' => Pages\CreatePelaporan::route('/create'),
            'edit' => Pages\EditPelaporan::route('/{record}/edit'),
            // CUSTOM PAGE
            'customers' => Pages\ListPelaporanCustomer::route(
                '/paket/{paket_umroh_id}/jadwal/{jadwal_keberangkatan_id}/customers'
            ),
            'manifests' => Pages\ListCustomerManifest::route(
                '/paket/{paket_umroh_id}/jadwal/{jadwal_keberangkatan_id}/manifests'
            ),

        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Booking::query()
            ->join('customers', 'customers.id', '=', 'bookings.customer_id')
            ->join('jadwal_keberangkatans', 'jadwal_keberangkatans.id', '=', 'bookings.jadwal_keberangkatan_id')
            ->join('paket_umrohs', 'paket_umrohs.id', '=', 'bookings.paket_umroh_id')
            ->select([
                'bookings.paket_umroh_id',
                'bookings.jadwal_keberangkatan_id',

                // ðŸ“¦ DATA PAKET
                'paket_umrohs.harga_paket as harga_paket',

                // ðŸ“… JADWAL
                'jadwal_keberangkatans.tanggal_keberangkatan as tanggal_start',
                'jadwal_keberangkatans.tanggal_kembali as tanggal_end',

                // ðŸ“Š AGREGASI
                DB::raw('COUNT(bookings.id) as total_booking'),
                DB::raw('SUM(bookings.total_price) as total_revenue'),

                DB::raw("
                SUM(
                    CASE
                        WHEN customers.jenis_kelamin = 'laki-laki' THEN 1
                        ELSE 0
                    END
                ) as laki_laki
            "),

                DB::raw("
                SUM(
                    CASE
                        WHEN customers.jenis_kelamin = 'perempuan' THEN 1
                        ELSE 0
                    END
                ) as perempuan
            "),
            ])
            ->whereNotIn('bookings.status', ['canceled', 'draft'])
            ->groupBy(
                'bookings.paket_umroh_id',
                'bookings.jadwal_keberangkatan_id',
                'paket_umrohs.harga_paket',
                'jadwal_keberangkatans.tanggal_keberangkatan',
                'jadwal_keberangkatans.tanggal_kembali'
            );
    }
}
