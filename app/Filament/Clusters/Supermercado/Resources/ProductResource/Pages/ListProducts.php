<?php

namespace App\Filament\Clusters\Supermercado\Resources\ProductResource\Pages;

use App\Filament\Clusters\Supermercado\Resources\ProductResource;
use App\Models\Product;
use App\Models\Supermarket;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->mutateFormDataUsing(
                    function (array $data): array {

                        if ($data['all']) {
                            $data['supermarket_id'] = Supermarket::first()->id;
                        }
                        return $data;
                    }
                )
                ->after(
                    function (array $data) {
                        if ($data['all']) {
                            $supermercados = Supermarket::all();
                            $first_id = Supermarket::first()->id;
                            foreach ($supermercados as $supermercado) {
                                if ($supermercado->id != $first_id) {
                                    Product::create([
                                        'name' => $data['name'],
                                        'category_id' => $data['category_id'],
                                        'unit_id' => $data['unit_id'],
                                        'units_quantity' => $data['units_quantity'],
                                        'price' => $data['price'],
                                        'image' => null,
                                        'supermarket_id' => $supermercado->id
                                    ]);
                                }
                            }
                        }
                    }
                ),
        ];
    }
}
