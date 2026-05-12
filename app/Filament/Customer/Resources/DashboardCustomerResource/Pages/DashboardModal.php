<?php

namespace App\Filament\Customer\Resources\DashboardCustomerResource\Pages;

use App\Filament\Customer\Resources\DashboardCustomerResource;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Session;

class DashboardModal extends Page
{
    protected static string $resource = DashboardCustomerResource::class;

    protected static string $view = 'filament.customer.resources.dashboard-customer-resource.pages.dashboard-modal';


    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;

    public function mount(): void
    {
        // Tampilkan hanya sekali setelah login
        if (! session()->has('dashboard_modal_shown')) {
            session()->put('dashboard_modal_shown', true);

            $this->dispatch('open-dashboard-modal');
        }
    }

    protected function getActions(): array
    {
        return [
            Action::make('welcomeModal')
                ->modalHeading('Selamat Datang 🎉')
                ->modalDescription('Selamat datang di Dashboard Customer ADW Travel.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Mengerti')
                ->extraAttributes([
                    'x-on:open-dashboard-modal.window' => '$wire.mountAction("welcomeModal")',
                ]),
        ];
    }


}
