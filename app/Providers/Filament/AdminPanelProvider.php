<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Cart;
use Asmit\ResizedColumn\ResizedColumnPlugin;
use Awcodes\Gravatar\GravatarPlugin;
use Awcodes\Gravatar\GravatarProvider;
use Filament\Widgets\AccountWidget;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Auth\Pages\EditProfile;
use Filament\Enums\GlobalSearchPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->globalSearch( false)
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class)
            ->maxContentWidth(Width::Full)
            ->brandName(config('app.name'))
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            //->darkMode(false)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Cart::class,
                //Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,


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
                TrimStrings::class
            ])
            ->defaultAvatarProvider(GravatarProvider::class)
            ->plugins([
                FilamentShieldPlugin::make(),
                GravatarPlugin::make(),
                 ResizedColumnPlugin::make()

            ])

            ->authMiddleware([
                Authenticate::class,

            ]);
    }
}
