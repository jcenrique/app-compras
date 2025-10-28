<?php

namespace App\Filament\App\Resources\MarketResource\Pages;

use App\Filament\App\Resources\MarketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarkets extends ListRecords
{
    protected static string $resource = MarketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->createAnother(false),
        ];
    }
}
