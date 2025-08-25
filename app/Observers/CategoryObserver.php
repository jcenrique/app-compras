<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class CategoryObserver
{
    /**
     * Handle the Category "creating" event.
     */
    public function creating(Category $category): void
    {
        // Automatically generate slug if not provided
        if (empty($category->slug)) {
            $sectionName = Section::find($category->section_id)->name ?? '';
            $category->slug = str($sectionName  . '-' . $category->name)->slug();
        }
    }
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {

    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        // image update logic can be added here if needed
        if ($category->isDirty('image') && $category->getOriginal('image')) {
            // Handle the image update logic, e.g., delete old image if necessary
            Storage::disk('public')->delete($category->getOriginal('image'));
        }
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        // image deletion logic can be added here if needed
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
            // Assuming you have a method to delete the image file
            // You can also use $category->image->delete() if you are using a model for the image
        }
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }


}
