<?php

namespace App\Filament\Resources\Sections\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Sections\Sections\SectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSection extends EditRecord
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
