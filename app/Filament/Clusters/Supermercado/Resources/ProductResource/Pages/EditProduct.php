<?php

namespace App\Filament\Clusters\Supermercado\Resources\ProductResource\Pages;

use App\Filament\Clusters\Supermercado\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
