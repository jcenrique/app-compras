<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['client_id'] = Auth::user()->id;

                    return $data;
                })

                ->createAnother(false)
                ->successRedirectUrl(function(Order $record){
                     return EditOrder::getUrl(['record' => $record]);
                })

        ];
    }
}
