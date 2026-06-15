<?php


namespace App\Filament\Resources\DashboardAdminResource\Pages;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\DashboardAdminResource;


class Dashboard extends Page
{
    protected static string $resource = DashboardAdminResource::class;

    protected static string $view = 'filament.resources.dashboard-admin-resource.pages.dashboard';

   public function getStats(): array
    {
        $totalCustomer = Customer::whereHas('user', function ($query) {
            $query->where('role', 'customer');
        })->count();

        $totalBooking = Booking::count();

        $totalPayment = Payment::where('status', 'verified')
            ->sum('jumlah_bayar');

        $totalTagihan = Booking::sum('total_price');

        return [
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
            [
                'label' => 'Total Uang Masuk',
                'value' => 'Rp ' . number_format($totalPayment, 0, ',', '.'),
                'description' => 'Pembayaran terverifikasi',
                'icon' => 'heroicon-o-banknotes',
                'color' => 'info',
            ],
            [
                'label' => 'Total Estimasi Pendapatan',
                'value' => 'Rp ' . number_format($totalTagihan, 0, ',', '.'),
                'description' => 'Estimasi Pendapatan',
                'icon' => 'heroicon-o-banknotes',
                'color' => 'info',
            ],
        ];
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }
}
