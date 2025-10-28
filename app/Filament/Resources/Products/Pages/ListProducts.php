<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\Products\ProductResource;
use App\Models\Market;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),

            // crear una accion para actualizar el precio de los productos
            Action::make('update_price')
                ->label(__('common.update_price'))
                ->action('updatePrice')
                ->requiresConfirmation()
                ->color('success'),

            Action::make('sincronizarMercadona')
                ->label('Sincronizar productos Mercadona')
                ->action('syncMercadonaBatch')
                ->requiresConfirmation()
                ->color('warning'),

        ];
    }

    // funcion para actualizar los precios de los productos de Mercadona
    public function updatePrice(): void
    {
        $output = Artisan::call('products:extract-market-id');
        Notification::make()
            ->title('Actualizar precios y productos')
            ->body('Función en proceso de desarrollo (solo disponible para Mercadona)'
                .PHP_EOL.'Salida del comando:'.PHP_EOL.$output)
            ->color('danger')
            ->icon('fab-connectdevelop')
            ->iconColor('danger')
            ->duration(0)
            ->send();
    }

    public function syncMercadonaBatch(): void
    {
        // $ids = Product::whereHas('market.name', 'Mercadona')->pluck('market_product_id')->toArray();
        $ids = Product::whereHas('market', function ($query) {
            $query->where('name', 'Mercadona');
        })->get()->pluck('market_product_id')->toArray();


        $idsActualizados = [];

        foreach ($ids as $id) {
            try {
                $this->syncMercadonaProduct($id);
                $idsActualizados[] = $id;
            } catch (\Exception $e) {
                Log::warning("Error al sincronizar producto {$id}: ".$e->getMessage());
            }
        }

        Product::whereNotIn('market_product_id', $idsActualizados)
            ->update(['active' => false]);
    }

    public function syncMercadonaProduct(int $id): void
    {
        $response = Http::get("https://tienda.mercadona.es/api/products/{$id}");

        if ($response->failed()) {
            throw new \Exception("Error al obtener el producto {$id}");
        }

        $data = $response->json();

        $product = Product::where('market_product_id' , $id)->first();
        if (! $product) {
            throw new \Exception("Producto con ID {$id} no encontrado en la base de datos");
        } else {
            Log::info("Actualizando producto: {$product->name} (ID: {$product->id})");
            // product is package
            $productFormat = null;
           
            if ($data['price_instructions']['is_pack']) {
                // calcular el formato en funcion del pack size y total units
                $divisor = 1;
                $peso = 'kg';
                if ($data['price_instructions']['pack_size'] < 1) {
                    $divisor = 1000;
                    $peso = 'g';
                }
                if ($data['price_instructions']['size_format'] == 'kg') {
                    $productFormat = ' ('.$data['price_instructions']['total_units'].' '
                                     .$data['price_instructions']['unit_name'].' x '
                                     .$data['price_instructions']['pack_size'] * $divisor.' '
                                     .$peso.')';
                } elseif ($data['price_instructions']['size_format'] == 'l') {
                    $peso = 'l';
                    $productFormat = ' ('.$data['price_instructions']['total_units'].' '
                                     .$data['price_instructions']['unit_name'].' x '
                                     .$data['price_instructions']['pack_size'].' '
                                     .$peso.')';
                } else {
                    $productFormat = ' ('.$data['price_instructions']['total_units'].' '
                                    .$data['price_instructions']['unit_name'].' x '
                                    .$data['price_instructions']['pack_size'] * $divisor.' '
                                    .$data['price_instructions']['size_format'].')';
                }

            }
          

            $product->update([
              //  'name' => $product->name,
                'price' => $data['price_instructions']['unit_price'] ?? null,
                'active' => $data['published'] ?? false,
                'format' => $productFormat,
            ]);
            $product->save();
        }
    }

       public function setPage($page, $pageName = 'page') :void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

}
