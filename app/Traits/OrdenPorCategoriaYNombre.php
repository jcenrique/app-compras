<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait OrdenPorCategoriaYNombre
{
    public function scopeOrdenPorCategoriaYNombre(Builder $query): Builder
    {
        return $query
            ->withAggregate('category', 'name') // crea alias categoria_name
            ->orderBy('category_name')          // ordena por nombre de categoría
            ->orderBy('name');
          //  ->select('products.*'); // importante para evitar conflictos
    }
}
