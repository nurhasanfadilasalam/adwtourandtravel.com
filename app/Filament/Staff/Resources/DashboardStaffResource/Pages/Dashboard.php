<?php

namespace App\Filament\Staff\Resources\DashboardStaffResource\Pages;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use Filament\Resources\Pages\Page;
use App\Filament\Staff\Resources\DashboardStaffResource;


class Dashboard extends Page
{
    protected static string $resource = DashboardStaffResource::class;

    protected static string $view = 'filament.staff.resources.dashboard-staff-resource.pages.dashboard';


    public function getStats(): array
    {
        
        $staffId = auth()->id();
        
        $totalCustomer = Customer::whereHas('user', function ($query) {
            $query->where('role', 'customer');
        })->count();

        $totalBooking = Booking::where('created_by', $staffId)->count();

        $totalPayment = Payment::where('status', 'verified')
            ->sum('jumlah_bayar');

        $totalTagihan = Booking::where('status', ['partial', 'paid'])
            ->sum('total_price');

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
