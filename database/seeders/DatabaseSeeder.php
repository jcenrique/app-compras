<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use App\Models\Section;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supermarket;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' =>  Hash::make('1234qwer'),
        ]);

        //cargar unidades json
        // Ruta del archivo JSON en la carpeta 'database'
        $rutaArchivo = database_path('units.json');

        // Leer el contenido del archivo
        $contenidoJson = file_get_contents($rutaArchivo);

        // Convertir el JSON en un array asociativo
        $datos = json_decode($contenidoJson, true);

        foreach ($datos['units'] as $key => $value) {


            Unit::factory()->create([
                'name' => $value
            ]);
        }

        // supermercados
        $rutaArchivo = database_path('supermarkets.json');

        // Leer el contenido del archivo
        $contenidoJson = file_get_contents($rutaArchivo);

        // Convertir el JSON en un array asociativo
        $datos = json_decode($contenidoJson, true);

        foreach ($datos['supermarkets'] as $key => $value) {


            Supermarket::factory()->create([
                'name' => $value,
                'slug' => Str::slug($value)
            ]);
        }



        // secciones
        $rutaArchivo = database_path('sections.json');

        // Leer el contenido del archivo
        $contenidoJson = file_get_contents($rutaArchivo);

        // Convertir el JSON en un array asociativo
        $datos = json_decode($contenidoJson, true);

        foreach ($datos['sections'] as $key => $value) {


            Section::factory()->create([
                'name' => $value['name'],
                'description' => $value['description'],
                'slug' => Str::slug($value['name'])
            ]);
        }




        //categorias
        // secciones
        $rutaArchivo = database_path('categories.json');

        // Leer el contenido del archivo
        $contenidoJson = file_get_contents($rutaArchivo);

        // Convertir el JSON en un array asociativo
        $datos = json_decode($contenidoJson, true);

        foreach ($datos['categorias'] as $value) {
            // dd($value);
            $section_id = Section::where('name', $value['seccion'])->first()->id;
            foreach ($value['productos'] as  $categoria) {
                Category::factory()->create([
                    'section_id' => $section_id,
                    'name' => $categoria,
                    'slug' => Str::slug($categoria)
                ]);
            }
        }



        // secciones
        $rutaArchivo = database_path('producto.json');

        // Leer el contenido del archivo
        $contenidoJson = file_get_contents($rutaArchivo);

        // Convertir el JSON en un array asociativo
        $datos = json_decode($contenidoJson, true);

        foreach ($datos['supermercados'] as $supermercado) {
            // dd($value);
            $supermercado_id = Supermarket::where('name', $supermercado['nombre'])->first()->id;

            foreach ($supermercado['secciones'] as  $section) {
                $section_id = Section::where('name', $section['nombre'])->first()->id;

                foreach ($section['categorias'] as  $category) {
                    $category_id = Category::where('name', $category['categoria'])->first()->id;
                    
                    foreach ($category['productos'] as  $product) {
                        Product::create([
                            'supermarket_id' => $supermercado_id,
                            'category_id' => $category_id,
                            'name' => $product['nombre'],
                            'unit_id' => 1,
                            'price' =>  $product['precio'],
                            'units_quantity' => 1
                           
                        ]);
                    }
                }
            }
        }
    }
}
