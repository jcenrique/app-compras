<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Market;
use App\Models\Product;
use Filament\Actions;
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
                ->createAnother(false)
        ];
    }
}
