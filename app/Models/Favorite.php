<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    //modelo de los productos favoritos por cliente


    protected $fillable = [
        'client_id',
        'product_id',
    ];
    //marcar como favorito
    public function markAsFavorite(): void
    {
        $this->is_favorite = true;
        $this->save();
    }
    //relación con el modelo de cliente
    // public function client()
    // {
    //     return $this->belongsTo(Client::class);
    // }

    // //relación con el modelo de producto
    // public function products()
    // {
    //     return $this->belongsToMany(Product::class);
    // }
}
