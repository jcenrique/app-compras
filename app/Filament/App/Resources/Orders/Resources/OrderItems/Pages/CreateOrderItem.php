<?php

namespace App\Filament\App\Resources\Orders\Resources\OrderItems\Pages;

use App\Filament\App\Resources\Orders\Resources\OrderItems\OrderItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderItem extends CreateRecord
{
    protected static string $resource = OrderItemResource::class;
}
