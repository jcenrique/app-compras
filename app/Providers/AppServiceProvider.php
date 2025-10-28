<?php

namespace App\Providers;

use Filament\Support\View\Components\ModalComponent;

use BezhanSalleh\PanelSwitch\PanelSwitch;

use Illuminate\Support\ServiceProvider;

use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

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

        Table::configureUsing(fn(Table $table) => $table
            ->deferFilters(false));
        Filament::serving(function () {
            Filament::registerTheme(app(Vite::class)(['resources/css/app.css']));
        });
        FilamentColor::register([
            'fuchsia' => Color::Fuchsia,
        ]);
        FileUpload::configureUsing(fn(FileUpload $fileUpload) => $fileUpload
            ->visibility('public'));

        ImageColumn::configureUsing(fn(ImageColumn $imageColumn) => $imageColumn
            ->visibility('public'));

        ImageEntry::configureUsing(fn(ImageEntry $imageEntry) => $imageEntry
            ->visibility('public'));
        TextInput::configureUsing(function (TextInput $input) {
            $input->mutateDehydratedStateUsing(function ($state) {
                return Str::trim($state);
            });
        });
     


        ModalComponent::closedByClickingAway(false);
        ModalComponent::closedByEscaping(false);
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
            $defaultLocale = config('app.locale', 'es'); // Obtiene el idioma por defecto
            $switch
                ->outsidePanelPlacement(Placement::BottomRight)
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
