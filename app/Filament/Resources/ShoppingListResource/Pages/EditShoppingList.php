<?php

namespace App\Filament\Resources\ShoppingListResource\Pages;

use App\Filament\Resources\ShoppingListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShoppingList extends EditRecord
{
    protected static string $resource = ShoppingListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
