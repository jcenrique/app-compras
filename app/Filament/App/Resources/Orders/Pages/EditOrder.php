<?php

namespace App\Filament\App\Resources\Orders\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\App\Resources\Orders\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
