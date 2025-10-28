<?php

namespace App\Filament\App\Resources\ProductResource\Pages;

use App\Filament\App\Resources\ProductResource;
use App\Models\Favorite;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data, string $model): Model {
                  
                    $record = $model::create($data);
                    if ($data['is_favorite']) {
                        $client_id = Auth::id();

                      
                        Favorite::create([
                            'client_id' => $client_id,
                            'product_id' => $record->id
                        ]);
                    }

                    // action that needs to be done 
                    return $record;
                })
                ->createAnother(false),

            
           


        ];
    }




    // function ablancodev_get_categories()
    // {


    //     //   DB::table('sections')->truncate();

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://tienda.mercadona.es/api/categories/');
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     $data = curl_exec($ch);
    //     curl_close($ch);

    //     if ($data) {
    //         $categorias = json_decode($data);

    //         if (isset($categorias->results)) {
    //             //secciones
    //             foreach ($categorias->results as $category) {

    //                 //create section
    //                 $newSection = Section::create([
    //                     //'id' => $category->id,
    //                     'name' => $category->name,
    //                     //  'description' => $category->order,

    //                 ]);

    //                 foreach ($category->categories as $cat) {

    //                     // crear categoria
    //                     $category = Category::create([
    //                         'name' => $cat->name,
    //                         'section_id' => $newSection->id,
    //                         // 'is_extended' => $cat->is_extended
    //                     ]);
    //                     $this->ablancodev_get_category($cat->id, $category);
    //                 }
    //                 // Llamamos a dicha categoría para ver si tiene más niveles

    //             }
    //         }
    //     }
    // }

    // function ablancodev_get_category($category_id, Category $category_create)
    // {

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://tienda.mercadona.es/api/categories/' . $category_id);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     $data = curl_exec($ch);
    //     curl_close($ch);

    //     if ($data) {
    //         echo 'Categoria: ' . $category_create->name . ' - ' . $category_create->id . "\n";
    //         $category = json_decode($data);

    //         if (isset($category->categories)) {


    //             foreach ($category->categories as $cat_info) {

    //                 foreach ($cat_info->products as $product) {
    //                     $productDetails = $this->getProductById($product->id);
    //                     if ($productDetails) {

    //                         //  echo '          Producto: ' .    $productDetails->display_name . '- '.  $productDetails->price_instructions->unit_price . "\n";

    //                         // crear producto
    //                         $description = '<div>' .
    //                             $productDetails->details->description . "\n" .
    //                             $productDetails->price_instructions->unit_size . ' ' . $productDetails->price_instructions->size_format . '</div>' .
    //                             '<div>Ingredientes: <strong style="text-decoration: underline;">' .
    //                             $productDetails->nutrition_information->ingredients .
    //                             '</strong></div>' .
    //                             '<div>Alérgenos: <strong style="text-decoration: underline;">' .
    //                             $productDetails->nutrition_information->allergens .
    //                             '</strong></div>' .
    //                             '</div>' . "\n";
    //                         // recuperar imagen de la URL  y guardarla en la carpeta de productos
    //                         $image = file_get_contents($productDetails->thumbnail);

    //                         $imageName = Str::of(basename($productDetails->thumbnail))->explode('?')->first();

    //                         $result = file_put_contents(storage_path('app/public/images/products/' . $imageName), $image);

    //                         if ($result === false) {
    //                             $fileimage = '';
    //                         } else {
    //                             $fileimage = '/images/products/' .   $imageName;
    //                         }
    //                         //comprobar el slug para ver si el producto esta duplicado en la base de datos
    //                         $existingProduct = Product::where('slug', $productDetails->slug . '_' . $productDetails->id)->first();

    //                         if ($existingProduct) {
    //                             echo 'El producto ' . $productDetails->display_name . ' ya existe en la base de datos.' . "\n";

    //                         } else {
    //                             Product::create([
    //                                 'name' => $productDetails->display_name,
    //                                 'brand' => $productDetails->details->brand,
    //                                 'category_id' => $category_create->id,
    //                                 'price' => $productDetails->price_instructions->unit_price,
    //                                 'slug' => $productDetails->slug . '_' . $productDetails->id,
    //                                 'description' => $description,
    //                                 'image' => $fileimage,
    //                                 'market_id' => 1

    //                             ]);
    //                         }
    //                     }
    //                 }

    //                 // Llamamos a dicha categoría para ver si tiene más niveles
    //                 // $this->ablancodev_get_category($cat_info->id);

    //             }
    //         }
    //     }
    // }

    // function getProductById($product_id)
    // {
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://tienda.mercadona.es/api/products/' . $product_id);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     $data = curl_exec($ch);
    //     curl_close($ch);

    //     if ($data) {
    //         return json_decode($data);
    //     }

    //     return null;
    // }
}
