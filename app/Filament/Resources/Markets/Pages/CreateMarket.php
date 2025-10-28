<?php

namespace App\Filament\Resources\Markets\Pages;

use App\Filament\Resources\Markets\Markets\MarketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMarket extends CreateRecord
{
    protected static string $resource = MarketResource::class;
}
