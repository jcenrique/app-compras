<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Market;
use App\Models\Product;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data, string $model) {

                    $markets = Market::active()->get();
                    if (!$data['is_unique_market']) {


                        foreach ($markets as $market) {


                            Product::create([
                                'name' =>  $data['name'],
                                // 'slug' => str($product->name)->slug() . '-' . str($product->brand)->slug() . '-' . str($market->name)->slug(),
                                'brand' => $data['brand'],

                                'category_id' => $data['category_id'],
                                'market_id' => $market->id,
                                'image' => $data['image'],
                                'price' => $data['price'],
                                'description' => $data['description'],

                            ]);
                        }

                    }else {
                         $model::create($data);
                    }

                })
                ->createAnother(false),

                 // crear una accion para actualizar el precio de los productos
            Actions\Action::make('update_price')
                ->label(__('common.update_price'))
                ->action('updatePrice')
                ->requiresConfirmation()
                ->color('success'),
        ];
    }

    //funcion para actualizar los precios de los productos de Mercadona
    public function updatePrice(): void
    {
       Notification::make()
        ->title('Actualizar precios y productos')
        ->body('Función en proceso de desarrollo (solo disponible para Mercadona)')
        ->color('danger')
        ->icon('fab-connectdevelop')
        ->iconColor('danger')
        ->duration(0)
        ->send();

    }
}
