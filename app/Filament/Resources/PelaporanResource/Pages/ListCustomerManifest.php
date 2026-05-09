<?php

namespace App\Filament\Resources\PelaporanResource\Pages;

use stdClass;
use Carbon\Carbon;
use Filament\Tables;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaketUmroh;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use App\Models\JadwalKeberangkatan;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookingResource;
use App\Filament\Resources\PelaporanResource;
use App\Exports\CustomerManifestExport;
use Maatwebsite\Excel\Facades\Excel;



class ListCustomerManifest extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = PelaporanResource::class;

    protected static string $view = 'filament.resources.pelaporan-resource.pages.list-customer-manifest';

    public int $paket_umroh_id;

    public int $jadwal_keberangkatan_id;

    public string $namaPaket = '';
    public string $periode = '';
    public int $totalCustomer = 0;
    public array $totalBayarPerCustomer = [];
    public array $totalBayarPerBooking = [];
    public array $sisaTagihanPerBooking = [];

    protected function getTableHeaderActions(): array
    {
        return [
            // Action::make('cetakPdf')
            //     ->label('Cetak PDF')
            //     ->icon('heroicon-o-printer')
            //     ->color('danger')
            //     ->action('generatePdf'),

            Action::make('cetakExcel')
                ->label('Cetak Excel')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->action('generateExcel'),
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
                ->label('Nama')
                ->searchable(),
            TextColumn::make('customer.tgl_lahir')
                ->label('Tgl Lahir')
                ->date()
                ->sortable(),
            TextColumn::make('customer.tempat_lahir')
                ->label('Tempat Lahir')
                ->searchable(),

            TextColumn::make('customer.jenis_kelamin')
                ->label('Jenis Kelamin')
                ->badge(),

            Tables\Columns\TextColumn::make('customer.no_hp')
                ->label('No HP')
                ->alignCenter()
                ->searchable(),

            Tables\Columns\TextColumn::make('customer.no_passport')
                ->label('No Passpor')
                ->searchable(),
            Tables\Columns\TextColumn::make('customer.nama_ayah')
                ->label('Nama Ayah'),
            Tables\Columns\TextColumn::make('customer.kota_passport')
                ->label('Kota Passpor'),
            // Tables\Columns\TextColumn::make('customer.tgl_dikeluarkan_passport')
            //     ->date()
            //     ->label('Tgl Passpor'),
            // Tables\Columns\TextColumn::make('customer.tgl_habis_passport')
            //     ->date()
            //     ->label('Tgl Expire Passpor'),
            Tables\Columns\TextColumn::make('customer.tgl_passport')
                ->label('Tanggal Passport')
                ->columnSpan(3)
                ->alignRight()
                ->html(true)
                ->getStateUsing(function ($record) {
                    // Format the 'tgl_dikeluarkan_passport' and 'tgl_habis_passport' dates
                    $data1 = Carbon::parse($record->tgl_dikeluarkan_passport)->format('Y-m-d');  // Format the issued date
                    $data2 = Carbon::parse($record->tgl_habis_passport)->translatedFormat('l, d F Y');

                    // Concatenate with <br> for line breaks between the two
                    return 'Start  : ' . $data1 . '<br>' . 'End    : ' . $data2;
                }),
            Tables\Columns\TextColumn::make('customer.nama_passport')
                ->label('Nama Passport')
                ->searchable(),

            Tables\Columns\TextColumn::make('customer.provinsi')
                ->label('Provinsi')
                ->searchable(),
            Tables\Columns\TextColumn::make('customer.kota_kabupaten')
            ->label('Kabupaten')
                ->searchable(),
            Tables\Columns\TextColumn::make('customer.kewarganegaraan')
                ->label('Kewarganegaraan')
                ->searchable(),
            Tables\Columns\TextColumn::make('customer.status_pernikahan')
            ->label('Status Pernikahan')
                ->searchable(),
            Tables\Columns\TextColumn::make('customer.jenis_pendidikan')
                ->label('Pendidikan Terakhir')
                ->searchable(),
            Tables\Columns\TextColumn::make('customer.jenis_pekerjaan')
            ->label('Pekerjaan')
                ->searchable(),
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

    public function generateExcel()
    {
        $filename =
                'Manifest-' .
                str($this->namaPaket)->slug() .
                '-' .
                now()->format('Ymd_His') .
                '.xlsx';

            return Excel::download(
                new CustomerManifestExport(
                    $this->paket_umroh_id,
                    $this->jadwal_keberangkatan_id
                ),
                $filename
            );
    }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'created_at';
    }
}
