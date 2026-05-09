<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerManifestExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected int $paketId;
    protected int $jadwalId;
    protected array $totalBayarPerBooking = [];

    public function __construct(int $paketId, int $jadwalId)
    {
        $this->paketId = $paketId;
        $this->jadwalId = $jadwalId;

        $this->preparePayments();
    }

    protected function preparePayments(): void
    {
        $bookingIds = Booking::where('paket_umroh_id', $this->paketId)
            ->where('jadwal_keberangkatan_id', $this->jadwalId)
            ->pluck('id');

        $this->totalBayarPerBooking = Payment::query()
            ->whereIn('booking_id', $bookingIds)
            ->where('status', 'approved')
            ->selectRaw('booking_id, SUM(jumlah_bayar) as total')
            ->groupBy('booking_id')
            ->pluck('total', 'booking_id')
            ->toArray();
    }

    public function collection(): Collection
    {
        return Booking::with(['customer', 'creator'])
            ->where('paket_umroh_id', $this->paketId)
            ->where('jadwal_keberangkatan_id', $this->jadwalId)
            ->whereNotIn('status', ['canceled', 'draft'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama KTP',
            'Nama Passport',
            'Jenis Kelamin',
            'No HP',
            'No Passport',
            'Tanggal Terbit Passport',
            'Tanggal Habis Passport',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Kewarganegaraan',
            'Agen',
            'Harga Paket',
            'Total Bayar',
            'Sisa Tagihan',
        ];
    }

    public function map($booking): array
    {
        static $no = 1;

        $totalBayar = $this->totalBayarPerBooking[$booking->id] ?? 0;
        $sisa = max($booking->total_price - $totalBayar, 0);

        return [
            $no++,
            $booking->customer->nama_ktp ?? '-',
            $booking->customer->nama_passport ?? '-',
            $booking->customer->jenis_kelamin ?? '-',
            $booking->customer->no_hp ?? '-',
            $booking->customer->no_passport ?? '-',
            optional($booking->customer->tgl_dikeluarkan_passport)->format('Y-m-d'),
            optional($booking->customer->tgl_habis_passport)->format('Y-m-d'),
            $booking->customer->tempat_lahir ?? '-',
            optional($booking->customer->tgl_lahir)->format('Y-m-d'),
            $booking->customer->kewarganegaraan ?? '-',
            $booking->creator->name ?? '-',
            $booking->total_price,
            $totalBayar,
            $sisa,
        ];
    }
}
