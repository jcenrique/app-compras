<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\Pages\EditOrder;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Orders\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                

                ->createAnother(false)
                ->successRedirectUrl(function(Order $record){
                     return EditOrder::getUrl(['record' => $record]);
                })

        
        ];
    }
}
