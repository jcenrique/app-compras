<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\Orders\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
