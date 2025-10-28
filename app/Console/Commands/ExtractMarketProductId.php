<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ExtractMarketProductId extends Command
{
     protected $signature = 'products:extract-market-id';
    protected $description = 'Extrae el ID del slug y lo guarda en market_product_id';

    public function handle()
    {
        $this->info('Iniciando extracción de market_product_id desde slug...');

        $productos = Product::whereNull('market_product_id')->get();
        $actualizados = 0;

        foreach ($productos as $producto) {
            // Buscar el último guion bajo y extraer lo que sigue
            if (preg_match('/_(\d+)$/', $producto->slug, $matches)) {
                $producto->market_product_id = (int) $matches[1];
                $producto->save();
                $actualizados++;
                $this->line("✔️ Producto ID {$producto->id} actualizado con market_product_id: {$producto->market_product_id}");
            } else {
                $this->warn("⚠️ No se encontró ID en slug: {$producto->slug}");
            }
        }

        $this->info("Proceso completado. Productos actualizados: {$actualizados}");
        return Command::SUCCESS;
    }
}
