<?php

namespace App\Filament\Clusters\Supermercado\Resources\SectionResource\Pages;

use App\Filament\Clusters\Supermercado\Resources\SectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSection extends EditRecord
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
