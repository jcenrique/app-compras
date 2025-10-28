<?php

namespace App\Filament\App\Resources\Markets\Pages;

use App\Filament\App\Resources\Markets\MarketResource;
use Filament\Actions\DeleteAction;


use Filament\Resources\Pages\EditRecord;

class EditMarket extends EditRecord
{
    protected static string $resource = MarketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
