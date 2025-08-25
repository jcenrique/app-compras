<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ShoppingListShoppingListProduct extends Pivot
{
    /** @use HasFactory<\Database\Factories\ShoppingListProductFactory> */
    use HasFactory;
    protected $table = 'shopping_list_shopping_list_product';

    protected $fillable = [
        'shopping_list_id',
        'product_id',
        'price',
        'quantity',
        'status'


    ];

    public $incrementing = true;

      protected function total(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->quantity * $this->price,
        );
    }

    public function shoppingLists(): BelongsToMany
    {
        return $this->belongsToMany(ShoppingList::class);
    }

    public function products(): BelongsToMany
    {
        return $this->BelongsToMany(Product::class);
    }



}
