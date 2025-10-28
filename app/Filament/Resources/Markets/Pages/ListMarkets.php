<?php

namespace App\Filament\Resources\Markets\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Markets\Markets\MarketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarkets extends ListRecords
{
    protected static string $resource = MarketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
