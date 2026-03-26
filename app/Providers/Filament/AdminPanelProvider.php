<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\File;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Shared/Resources'), for: 'App\Filament\Shared\Resources')
            ->discoverPages(in: app_path('Filament/Shared/Pages'), for: 'App\Filament\Shared\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Shared/Widgets'), for: 'App\Filament\Shared\Widgets')
            ->tap(function (Panel $panel) {
                // Auto-discover Filament resources, pages, and widgets for each template
                if (File::isDirectory(app_path('Filament/Templates'))) {
                    foreach (File::directories(app_path('Filament/Templates')) as $templatePath) {
                        $templateName = basename($templatePath);
                        $namespace = "App\\Filament\\Templates\\{$templateName}";
                        $panel
                            ->discoverResources(in: "{$templatePath}/Resources", for: "{$namespace}\\Resources")
                            ->discoverPages(in: "{$templatePath}/Pages", for: "{$namespace}\\Pages")
                            ->discoverWidgets(in: "{$templatePath}/Widgets", for: "{$namespace}\\Widgets");
                    }
                }
            })
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
