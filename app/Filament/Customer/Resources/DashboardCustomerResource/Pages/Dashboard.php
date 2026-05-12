<?php

namespace App\Filament\Customer\Resources\DashboardCustomerResource\Pages;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use App\Filament\Customer\Resources\DashboardCustomerResource;
use Filament\Resources\Pages\Page;


class Dashboard extends Page
{
    protected static string $resource = DashboardCustomerResource::class;

    protected static string $view = 'filament.customer.resources.dashboard-customer-resource.pages.dashboard';

    public function getStats(): array
    {
        $totalCustomer = Customer::whereHas('user', function ($query) {
            $query->where('role', 'customer');
        })->count();

        $totalBooking = Booking::count();

        $totalPayment = Payment::where('status', 'verified')
            ->sum('jumlah_bayar');

        $totalTagihan = Booking::where('status', 'partial')
            ->sum('total_price');

        return [
            [
                'label' => 'Selamat Datang di ADW Travel',
                'value' => $totalBooking,
                'description' => 'Data Customer Booked',
                'icon' => 'heroicon-o-calendar-days',
                'color' => 'success',
                'full' => true,
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
