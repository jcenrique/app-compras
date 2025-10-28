<?php

namespace App\Filament\Resources\Orders\Orders\Resources\OrderItems\Pages;

use App\Filament\Resources\Orders\Orders\Resources\OrderItems\OrderItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderItem extends CreateRecord
{
    protected static string $resource = OrderItemResource::class;
}
