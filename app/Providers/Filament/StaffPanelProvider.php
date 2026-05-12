<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class StaffPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('staff')
            ->path('staff')
            ->login()
            ->login(\App\Filament\Customer\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationGroups([
                'Kelola Customer',
                'Kelola Booking',
                'Kelola Pembayaran',
                'Kelola Jadwal',
                // 'Perlindung',
            ])
            ->discoverResources(
                in: app_path('Filament/Staff/Resources'),
                for: 'App\\Filament\\Staff\\Resources')
            ->discoverPages(
                in: app_path('Filament/Staff/Pages'),
                for: 'App\\Filament\\Staff\\Pages')
             ->resources([
                // Staff\Resources\CustomerResource::class,
            ])
            ->pages([
                // Pages\Dashboard::class,
                // \Pages\DashboardModal::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Staff/Widgets'),
                for: 'App\\Filament\\Staff\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
