<?php

namespace App\Providers;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Carbon::setLocale('id');

                // Add a link above the login form
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn(): string => Blade::render('<x-filament::link href="' . config('app.url') . '" size="sm" icon="heroicon-o-arrow-left">Halaman Landing Page</x-filament::link>')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            function (): string {

                $panel = Filament::getCurrentPanel()?->getId();

                // Jika LOGIN CUSTOMER → tampilkan link ke STAFF
                if ($panel === 'customer') {
                    return Blade::render(
                        '<x-filament::link
                    href="' . url('/staff/login') . '"
                    size="sm"
                    icon="heroicon-o-arrow-right">
                    Login sebagai Staff
                </x-filament::link>'
                    );
                }

                // Jika LOGIN STAFF → tampilkan link ke CUSTOMER
                if ($panel === 'staff') {
                    return Blade::render(
                        '<x-filament::link
                    href="' . url('/customer/login') . '"
                    size="sm"
                    icon="heroicon-o-arrow-right">
                    Login sebagai Customer
                </x-filament::link>'
                    );
                }

                return '';
            }
        );

        // Add a link above the register form
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_REGISTER_FORM_AFTER,
            fn(): string => Blade::render('<x-filament::link href="' . config('app.url') . '" size="sm" icon="heroicon-o-arrow-left">Halaman Landing Page</x-filament::link>')
        );
    }
}
