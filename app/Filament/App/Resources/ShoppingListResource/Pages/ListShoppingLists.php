<?php

namespace App\Filament\App\Resources\ShoppingListResource\Pages;

use App\Filament\App\Resources\ShoppingListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShoppingLists extends ListRecords
{
    protected static string $resource = ShoppingListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->createAnother(false)
            ->mutateFormDataUsing(function(array $data): array
                {
                    $data['user_id'] = auth()->id();

                    return $data;
                })

        ];
    }
}
