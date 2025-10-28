<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Cart;
use Asmit\ResizedColumn\ResizedColumnPlugin;
use Awcodes\Gravatar\GravatarPlugin;
use Awcodes\Gravatar\GravatarProvider;
use Filament\Widgets\AccountWidget;

use Filament\Auth\Pages\EditProfile;
use Filament\Enums\GlobalSearchPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;


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


class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('')
             ->authGuard('client') // this is the main addition
             ->authPasswordBroker('clients')
            ->globalSearch( false)
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class)
            ->brandLogo(asset('images/logo.png'))
            ->maxContentWidth(Width::Full)
            ->brandLogoHeight('3rem')
              // ->darkMode(false)
            ->viteTheme('resources/css/filament/app/theme.css')

            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Cart::class,
              //  Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
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

                GravatarPlugin::make(),
                 ResizedColumnPlugin::make()
            ])



            ->authMiddleware([
                Authenticate::class,

            ]);
    }
}
