<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Market;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;


class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/products.json'));
        $data = json_decode($json, true);
        foreach ($data as $productData) {
            \App\Models\Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'category_id' => Category::where('name', $productData['category'])->first()->id,
                'market_id' => Market::where('name', $productData['market'])->first()->id,
                'active' => true,
            ]);
        }
    }
}
