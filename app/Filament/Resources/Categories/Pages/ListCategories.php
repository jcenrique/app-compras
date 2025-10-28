<?php

namespace App\Filament\Resources\Categories\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()

                ->createAnother(false),
        ];
    }
}
