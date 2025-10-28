<?php

namespace App\Filament\App\Resources\Markets\Pages;

use App\Filament\App\Resources\Markets\MarketResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarkets extends ListRecords
{
        use HasResizableColumn;

    protected static string $resource = MarketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
