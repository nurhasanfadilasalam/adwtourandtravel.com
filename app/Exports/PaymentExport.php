<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class PaymentExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected int $maxCicilan = 0;
    protected Builder $query;

    // 1. Terima query dari Filament lewat constructor
    public function __construct(Builder $query)
    {
        // Query asal dari Payment, kita ambil booking_id yang unik saja
        $bookingIds = $query->pluck('booking_id')->unique()->toArray();

        // Buat query baru khusus untuk Booking berdasarkan ID tersebut
        $this->query = Booking::whereIn('id', $bookingIds)->with([
            'customer',
            'paketUmroh',
            'payments' => fn ($q) => $q->orderBy('tanggal_bayar'),
        ]);
    }

    /**
     * Ambil data booking + payment sesuai query yang difilter
     */
    public function collection(): Collection
    {
        // 2. Jalankan query yang sudah difilter
        $bookings = $this->query->get();

        // Cari cicilan terbanyak
        $this->maxCicilan = $bookings
            ->map(fn ($b) => $b->payments->count())
            ->max() ?? 0;

        $rows = collect();
        $no = 1;

        foreach ($bookings as $booking) {
            $row = [
                $no++,
                $booking->customer?->nama_ktp,
                $booking->paketUmroh?->nama_paket,
                (float) $booking->total_price,
            ];

            // Isi cicilan
            foreach ($booking->payments as $payment) {
                $row[] = (float) $payment->jumlah_bayar;
            }

            // Jika cicilan kurang dari max → isi kosong
            while (count($row) < 4 + $this->maxCicilan) {
                $row[] = '';
            }

            $rows->push($row);
        }

        return $rows;
    }

    /**
     * Header dinamis
     */
    public function headings(): array
    {
        $headers = [
            'No',
            'Nama Customer',
            'Nama Paket',
            'Harga',
        ];

        for ($i = 1; $i <= $this->maxCicilan; $i++) {
            $headers[] = "Cicilan {$i}";
        }

        return $headers;
    }

    /**
     * Styling header
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '16A34A'],
                ],
            ],
        ];
    }
}