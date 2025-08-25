<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Support\View\Components\Modal;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
//use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

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
        Product::observe(ProductObserver::class);
        Modal::closedByClickingAway(false);
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es', 'fr', 'en'])
                ->flags([
                    'es' => asset('flags/es.svg'),
                    'fr' => asset('flags/fr.svg'),
                    'en' => asset('flags/us.svg'),
                ]); // also accepts a closure
        });

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {

            $panelSwitch->modalHeading(__('Available panels'))
                ->visible(true)
                ->modalWidth('sm')
                ->labels([
                    'admin' => __('Administration panel'),
                    'app' => __('Aplication panel')
                ])
                ->icons([
                    'app' => 'heroicon-o-square-2-stack',
                    'admin' => 'heroicon-o-star',
                ], $asImage = false);
        });
    }
}
