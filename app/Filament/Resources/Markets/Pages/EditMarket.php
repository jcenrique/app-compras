<?php

namespace App\Filament\Resources\Markets\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Markets\Markets\MarketResource;
use Filament\Actions;
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
