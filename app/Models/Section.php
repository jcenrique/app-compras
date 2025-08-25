<?php

namespace App\Models;

use App\Observers\SectionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;
/**
 * Class Section
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $slug
 * @property string|null $image
 * @property bool $active
 */
#[ObservedBy(SectionObserver::class)]
class Section extends Model  implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable=[
        'name',
        'slug',
        'description',
        'image',
        'active'
    ];

     /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

     /**
     * Get the categories associated with the section.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
      public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
