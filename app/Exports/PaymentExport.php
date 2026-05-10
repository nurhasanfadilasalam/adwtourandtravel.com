<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected int $maxCicilan = 0;

    /**
     * Ambil data booking + payment
     */
    public function collection(): Collection
    {
        $bookings = Booking::with([
            'customer',
            'paketUmroh',
            'payments' => fn ($q) => $q->orderBy('tanggal_bayar'),
        ])->get();

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
