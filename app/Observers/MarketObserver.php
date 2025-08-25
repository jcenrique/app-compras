<?php

namespace App\Observers;

use App\Models\Market;
use Illuminate\Support\Facades\Storage;

class MarketObserver
{
    /**
     * Handle the Market "creating" event.
     */
    public function creating(Market $market): void
    {
        // Automatically generate slug if not provided
        if (empty($market->slug)) {
            $market->slug = str($market->name)->slug();
        }
    }
    /**
     * Handle the Market "created" event.
     */
    public function created(Market $market): void
    {
        //
    }

    /**
     * Handle the Market "updated" event.
     */
    public function updated(Market $market): void
    {
        // logo update logic can be added here if needed
        if ($market->isDirty('logo') && $market->getOriginal('logo')) {
            // Handle the logo update logic, e.g., delete old logo if necessary
            Storage::disk('public')->delete($market->getOriginal('logo'));
        }
    }

    /**
     * Handle the Market "deleted" event.
     */
    public function deleted(Market $market): void
    {
        // logo deletion logic can be added here if needed
        if ($market->logo) {
            Storage::disk('public')->delete($market->logo);
            // Assuming you have a method to delete the logo file
            // You can also use $market->logo->delete() if you are using a model for the logo
        }
    }

    /**
     * Handle the Market "restored" event.
     */
    public function restored(Market $market): void
    {
        //
    }

    /**
     * Handle the Market "force deleted" event.
     */
    public function forceDeleted(Market $market): void
    {
        //
    }
}
