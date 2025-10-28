<?php

namespace App\Models;

use App\Observers\ProductObserver;
use App\Traits\OrdenPorCategoriaYNombre;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use \OwenIt\Auditing\Auditable as AuditingTrait;

/**
 * Class Product
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property float $price
 * @property int|null $category_id
 * @property int|null $market_id
 * @property string|null $image
 * @property bool $is_active
 * @property string|null $brand
 */
#[ObservedBy(ProductObserver::class)]
class Product extends Model implements Auditable
{
    use HasFactory, AuditingTrait;
    use OrdenPorCategoriaYNombre;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'category_id',
        'market_id',
        'image',
        'active',
        'brand',
        'format',
        'market_product_id'
    ];

    protected $casts = [
        'price' => 'float',
        'active' => 'boolean',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function market()
    {
        return $this->belongsTo(Market::class);
    }
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    //crear relacion para marcar el producto como favorito
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'favorites')->withPivot('client_id');
    }


}
