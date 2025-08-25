<?php

namespace App\Filament\Clusters\Supermercado\Resources\CategoryResource\Pages;

use App\Filament\Clusters\Supermercado\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
