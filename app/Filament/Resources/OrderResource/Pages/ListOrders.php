<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                

                ->createAnother(false)
                ->successRedirectUrl(function(Order $record){
                     return EditOrder::getUrl(['record' => $record]);
                })

        
        ];
    }
}
