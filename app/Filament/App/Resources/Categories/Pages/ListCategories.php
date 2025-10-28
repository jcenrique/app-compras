<?php

namespace App\Filament\App\Resources\Categories\Pages;

use App\Filament\App\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

      public function setPage($page, $pageName = 'page') :void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }
}
