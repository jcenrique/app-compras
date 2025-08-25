<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Illuminate\Support\ServiceProvider;
use App\Http\Responses\RegisterResponse;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Support\View\Components\Modal;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerTheme(app(Vite::class)(['resources/css/app.css']));
        });
        TextInput::configureUsing(function (TextInput $input) {
            $input->mutateDehydratedStateUsing(function ($state) {
                return Str::trim($state);
            });
        });
        Modal::closedByClickingAway(false);
        Modal::closedByEscaping(false);
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->modalHeading(__('common.panel_avalaible_titles.title'))
                ->visible(fn(): bool => auth()->user()?->hasAnyRole([
                    'admin',
                    'usuario',
                    'super_admin',
                ]))
                ->modalWidth('sm')
                ->panels(['admin', 'app'])
                ->labels([
                    'admin' => __('common.panel_avalaible_titles.admin'),
                    'app' => __('common.panel_avalaible_titles.app')
                ])
                ->icons([
                    'admin' => 'heroicon-o-square-2-stack',
                    'app' => 'heroicon-o-star',
                ], $asImage = false)
            ;
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es', 'fr', 'en', 'eu'])
                ->circular()
                ->flags([
                    'es' => asset('flags/es.svg'),
                    'fr' => asset('flags/fr.svg'),
                    'en' => asset('flags/us.svg'),

                    'eu' => asset('flags/es-pv.svg'),
                ]);
        });
    }
}
