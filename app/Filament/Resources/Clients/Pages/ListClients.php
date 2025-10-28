<?php

namespace App\Filament\Resources\Clients\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Clients\Clients\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
