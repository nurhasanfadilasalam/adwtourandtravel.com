<?php

namespace App\Filament\Staff\Resources\DashboardStaffResource\Pages;

use App\Filament\Staff\Resources\DashboardStaffResource;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\PaketUmroh;
use App\Models\Payment;
use Filament\Resources\Pages\Page;

class Dashboard extends Page
{
    protected static string $resource = DashboardStaffResource::class;

    protected static string $view = 'filament.staff.resources.dashboard-staff-resource.pages.dashboard';

    public function getStats(): array
    {
        $staffId = auth()->id();

        // 1. Statistik Utama
        $totalCustomer = Customer::whereHas('user', function ($query) {
            $query->where('role', 'customer');
        })->count();

        $totalBooking = Booking::where('created_by', $staffId)->count();

        $totalPayment = Payment::where('status', 'verified')
            ->sum('jumlah_bayar');

        $totalTagihan = Booking::where('status', ['partial', 'paid'])
            ->sum('total_price');

        // Array dasar untuk statistik utama
        $mainStats = [
            [
                'label' => 'Total Booking',
                'value' => $totalBooking,
                'description' => 'Data Customer Booked',
                'icon' => 'heroicon-o-calendar-days',
                'color' => 'success',
            ],
            [
                'label' => 'Total Customer',
                'value' => $totalCustomer,
                'description' => 'Data Customer Aktif',
                'icon' => 'heroicon-o-users',
                'color' => 'primary',
            ],
            // [
            //     'label' => 'Total Uang Masuk',
            //     'value' => 'Rp ' . number_format($totalPayment, 0, ',', '.'),
            //     'description' => 'Pembayaran terverifikasi',
            //     'icon' => 'heroicon-o-banknotes',
            //     'color' => 'info',
            // ],
            // [
            //     'label' => 'Total Estimasi Pendapatan',
            //     'value' => 'Rp ' . number_format($totalTagihan, 0, ',', '.'),
            //     'description' => 'Estimasi Pendapatan',
            //     'icon' => 'heroicon-o-banknotes',
            //     'color' => 'info',
            // ],
        ];

        // 2. Statistik Okupansi Paket Umroh (Dinamis)
        $paketStats = [];
        $pakets = PaketUmroh::where('is_active', true)->get();

        foreach ($pakets as $paket) {
            $sisa = $paket->sisa_kuota;
            $total = $paket->kuota;
            $terisi = $paket->used_quota;

            $paketStats[] = [
                'label' => $paket->nama_paket, // Nama paket menjadi label utama
                'value' => $terisi . ' / ' . $total . ' Seat', // Okupansi terisi / total
                'description' => $sisa > 0 ? 'Sisa Kuota: ' . $sisa . ' Seat' : 'Full Booked (Kuota Habis)',
                'icon' => 'heroicon-o-ticket', // Icon tiket/kursi untuk paket umroh
                'color' => $sisa > 0 ? 'success' : 'danger', // Hijau jika masih ada, merah jika habis
            ];
        }

        // Gabungkan statistik utama dan statistik paket umroh menjadi satu array tunggal
        return array_merge($mainStats, $paketStats);
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }
}
