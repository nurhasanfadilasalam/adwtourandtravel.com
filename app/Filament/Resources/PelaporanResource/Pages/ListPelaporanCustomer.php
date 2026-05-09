<?php

namespace App\Filament\Resources\PelaporanResource\Pages;

use stdClass;
use Filament\Tables;
use App\Models\Booking;
// use Filament\Pages\Page;
use App\Models\Payment;
use App\Models\PaketUmroh;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use App\Models\JadwalKeberangkatan;
use Filament\Tables\Actions\Action;
// use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookingResource;



class ListPelaporanCustomer extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = \App\Filament\Resources\PelaporanResource::class;

    protected static string $view = 'filament.resources.pelaporan-resource.pages.list-pelaporan-customer';

    public int $paket_umroh_id;

    public int $jadwal_keberangkatan_id;

    public string $namaPaket = '';
    public string $periode = '';
    public int $totalCustomer = 0;
    public array $totalBayarPerCustomer = [];
    public array $sisaBayarPerCustomer = [];
    public array $totalBayarPerBooking = [];
    public array $sisaTagihanPerBooking = [];

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('cetakPdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->action('generatePdf'),
        ];
    }

    public function getTitle(): string
    {
        return 'List ' . $this->namaPaket;
    }

    public function mount(int $paket_umroh_id, int $jadwal_keberangkatan_id): void
    {
        $this->paket_umroh_id = $paket_umroh_id;
        $this->jadwal_keberangkatan_id = $jadwal_keberangkatan_id;

        // =====================
        // DATA PAKET
        // =====================
        $this->namaPaket = PaketUmroh::query()
            ->whereKey($paket_umroh_id)
            ->value('nama_paket') ?? '-';

        // =====================
        // PERIODE JADWAL
        // =====================
        $jadwal = JadwalKeberangkatan::find($jadwal_keberangkatan_id);

        if ($jadwal) {
            $this->periode =
                $jadwal->tanggal_keberangkatan->translatedFormat('d M Y')
                . ' - ' .
                $jadwal->tanggal_kembali->translatedFormat('d M Y');
        }

        // =====================
        // AMBIL BOOKING VALID
        // =====================
        // $bookings = Booking::query()
        //     ->where('paket_umroh_id', $paket_umroh_id)
        //     ->where('jadwal_keberangkatan_id', $jadwal_keberangkatan_id)
        //     ->whereNotIn('status', ['canceled', 'draft'])
        //     ->get(['id', 'total_price']);

        $bookings = Booking::with(['customer', 'creator'])
            ->where('paket_umroh_id', $this->paket_umroh_id)
            ->where('jadwal_keberangkatan_id', $this->jadwal_keberangkatan_id)
            ->whereNotIn('status', ['canceled', 'draft'])
            ->get();

        $this->totalCustomer = $bookings->count();



        $bookingIds = $bookings->pluck('id');

        $this->totalBayarPerCustomer = Payment::query()
            ->whereIn('booking_id', $bookingIds)
            ->selectRaw('customer_id, SUM(jumlah_bayar) as total_bayar')
            ->groupBy('customer_id')
            ->pluck('total_bayar', 'customer_id')
            ->toArray();

        $this->sisaBayarPerCustomer = Payment::query()
            ->whereIn('booking_id', $bookingIds)
            ->selectRaw('customer_id, SUM(jumlah_bayar) as total_bayar')
            ->groupBy('customer_id')
            ->pluck('total_bayar', 'customer_id')
            ->toArray();


        // =====================
        // TOTAL BAYAR PER BOOKING
        // =====================
        $this->totalBayarPerBooking = \App\Models\Payment::query()
            ->whereIn('booking_id', $bookingIds)
            ->where('status', 'approved') // 🔥 penting: hanya yang valid
            ->selectRaw('booking_id, SUM(jumlah_bayar) as total_bayar')
            ->groupBy('booking_id')
            ->pluck('total_bayar', 'booking_id')
            ->toArray();

        // =====================
        // SISA TAGIHAN PER BOOKING
        // =====================
        foreach ($bookings as $booking) {
            $totalBayar = $this->totalBayarPerBooking[$booking->id] ?? 0;

            $this->sisaTagihanPerBooking[$booking->id] =
                max($booking->total_price - $totalBayar, 0);
        }
    }

    protected function getTableQuery(): Builder
    {
        return Booking::query()
            ->with(['customer', 'payments', 'creator'])
            ->where('paket_umroh_id', $this->paket_umroh_id)
            ->where('jadwal_keberangkatan_id', $this->jadwal_keberangkatan_id)
            ->whereNotIn('status', ['canceled', 'draft']);
    }

    protected function getTableColumns(): array
    {
        return [
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
            TextColumn::make('customer.nama_ktp')
                ->label('Nama Customer')
                ->searchable(),

            TextColumn::make('customer.jenis_kelamin')
                ->label('Jenis Kelamin')
                ->badge(),

            // TextColumn::make('customer.no_hp')
            //     ->label('No HP'),

            // TextColumn::make('booking_code')
            //     ->label('Kode Booking')
            //     ->copyable(),

            TextColumn::make('creator.name')
                ->label('Agen')
                ->searchable(),

            TextColumn::make('total_price')
                ->label('Harga')
                ->money('IDR', true),

            TextColumn::make('total_bayar')
                ->label('Total Bayar')
                ->state(
                    fn($record) =>
                    $this->totalBayarPerCustomer[$record->customer_id] ?? 0
                )
                ->money('IDR', true),

            TextColumn::make('sisa_tagihan')
                ->label('Sisa Tagihan')
                ->state(function ($record) {
                    $totalBayar = $this->totalBayarPerCustomer[$record->customer_id] ?? 0;
                    $sisaTagihan = $record->total_price - $totalBayar;
                    return max($sisaTagihan, 0);
                })
                ->money('IDR', true)
                ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                ->weight('bold'),
        ];
    }


    public function generatePdf()
    {
        $paket = PaketUmroh::findOrFail($this->paket_umroh_id);
        $jadwal = JadwalKeberangkatan::findOrFail($this->jadwal_keberangkatan_id);

        // $bookings = Booking::with('customer')
        //     ->where('paket_umroh_id', $this->paket_umroh_id)
        //     ->where('jadwal_keberangkatan_id', $this->jadwal_keberangkatan_id)
        //     ->whereNotIn('status', ['canceled', 'draft'])
        //     ->get();

        $bookings = Booking::with(['customer', 'creator'])
            ->where('paket_umroh_id', $this->paket_umroh_id)
            ->where('jadwal_keberangkatan_id', $this->jadwal_keberangkatan_id)
            ->whereNotIn('status', ['canceled', 'draft'])
            ->get();

        $bookingIds = $bookings->pluck('id');

        $totalBayarPerBooking = Payment::query()
            ->whereIn('booking_id', $bookingIds)
            ->selectRaw('booking_id, SUM(jumlah_bayar) as total_bayar')
            ->groupBy('booking_id')
            ->pluck('total_bayar', 'booking_id')
            ->toArray();

        $grandTotalBayar = 0;

        // $bookings->each(function ($booking) use ($totalBayarPerBooking) {
        //     $booking->total_bayar = $totalBayarPerBooking[$booking->id] ?? 0;
        // });

        $bookings->each(function ($booking) use ($totalBayarPerBooking, &$grandTotalBayar) {
            $booking->total_bayar = $totalBayarPerBooking[$booking->id] ?? 0;

            $booking->total_tagihan = max(
                $booking->total_price - $booking->total_bayar,
                0
            );

            $grandTotalBayar += $booking->total_bayar;
        });

        $periode =
            $jadwal->tanggal_keberangkatan->translatedFormat('d M Y')
            . ' - ' .
            $jadwal->tanggal_kembali->translatedFormat('d M Y');

        // dd($bookings);

        return response()->streamDownload(function () use ($paket, $periode, $bookings, $grandTotalBayar) {
            echo Pdf::loadView('reports.laporan-per-paket-report', [
                'paket' => $paket,
                'periode' => $periode,
                'customers' => $bookings,
                'grandTotalBayar' => $grandTotalBayar,
            ])->stream();
        }, 'Laporan-' . str($paket->nama_paket)->slug() . '.pdf');
    }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'created_at';
    }
}
