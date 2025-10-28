<?php

namespace App\Filament\Resources\Sections\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Sections\Sections\SectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSections extends ListRecords
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
