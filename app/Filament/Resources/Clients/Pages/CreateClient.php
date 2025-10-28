<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\Clients\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;
}
