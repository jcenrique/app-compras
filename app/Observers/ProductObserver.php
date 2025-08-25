<?php

namespace App\Observers;

use App\Models\Market;
use App\Models\Product;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{

    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {


        // Automatically generate slug if not provided
        if (empty($product->slug)) {
            $product->slug = str($product->name)->slug() . '-' . str($product->brand)->slug() . '-' . str($product->market->name)->slug();
        }




    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {


    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Handle any specific logic when a product is updated
        // For example, you might want to log changes or perform additional validations
        if ($product->isDirty('image') && $product->getOriginal('image')) {
            // Logic for when the image  changes
            // You can add logic to handle the image update, like deleting the old image if necessary
             Storage::disk('public')->delete($product->getOriginal('image'));

        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        // Handle any specific logic when a product is deleted
        // For example, you might want to delete the product's image if it exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
            // Assuming you have a method to delete the image file
            // You can also use $product->image->delete() if you are using a model for the image
        }
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
