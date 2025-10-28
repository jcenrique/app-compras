<?php

namespace App\Filament\App\Resources\Orders\Pages;


use Filament\Actions\CreateAction;
use App\Filament\App\Resources\Orders\OrderResource;
use App\Filament\App\Resources\Orders\Pages\EditOrder;
use App\Models\Order;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOrders extends ListRecords
{
    use HasResizableColumn;
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
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
