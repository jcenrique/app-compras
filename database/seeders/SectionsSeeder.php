<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class SectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/secctions_categories.json'));
    $data = json_decode($json, true);

    foreach ($data['supermercado']['secciones'] as $seccionData) {
        $seccion = Section::create([
            'name' => $seccionData['nombre'],
            'description' => $seccionData['descripcion'],
        ]);

        foreach ($seccionData['categorias'] as $nombreCategoria) {
            Category::create([
                'name' => $nombreCategoria['nombre'],
                'section_id' => $seccion->id,
                'description' => $nombreCategoria['descripcion'],
            ]);
        }
    }
    }
}
