<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\JadwalBelumAdaAsisten;
use App\Filament\Widgets\JadwalHariIni;
use App\Filament\Widgets\JadwalPerHariChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->favicon(asset('favicon/favicon.ico'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->databaseNotifications()
            ->plugin(
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        hasAvatars: false,
                        slug: 'my-profile'
                    )
                    ->enableTwoFactorAuthentication()
                    ->enableSanctumTokens(false)
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
                JadwalBelumAdaAsisten::class,
                JadwalPerHariChart::class,
                JadwalHariIni::class,
            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => Blade::render('
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">
                        &copy; ' . date('Y') . ' Lab Darmajaya. Developed by 
                        <a href="https://github.com/MadeRaditya" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">
                            Made Raditya
                        </a>.
                    </div>
                ')
            )
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->login()
            ->darkMode(true)
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Penjadwalan')
                    ->icon('heroicon-o-calendar'),

                NavigationGroup::make()
                    ->label('Manajemen Asisten')
                    ->icon('heroicon-o-users'),

                NavigationGroup::make()
                    ->label('Data Master') 
                    ->icon('heroicon-o-server-stack')
                    ->collapsed(), 
            ]);
    }
}
