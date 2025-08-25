<?php

namespace App\Observers;

use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class SectionObserver
{

    /**
     * Handle the Section "creating" event.
     */
    public function creating(Section $section): void
    {
        // Automatically generate slug if not provided
        if (empty($section->slug)) {
            $section->slug = str($section->name)->slug();
        }
    }
    /**
     * Handle the Section "created" event.
     */
    public function created(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "updated" event.
     */
    public function updated(Section $section): void
    {
         // image update logic can be added here if needed
        if ($section->isDirty('image') && $section->getOriginal('image')) {
            // Handle the image update logic, e.g., delete old image if necessary
            Storage::disk('public')->delete($section->getOriginal('image'));
        }
    }
    /**
     * Handle the Section "deleted" event.
     */
    public function deleted(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "restored" event.
     */
    public function restored(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "force deleted" event.
     */
    public function forceDeleted(Section $section): void
    {
        //
    }
}
