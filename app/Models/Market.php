<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\MarketObserver;
use \OwenIt\Auditing\Auditable as AuditingTrait;

/**
 * Class Market
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $logo
 * @property bool $active
 */
#[ObservedBy(MarketObserver::class)]
class Market extends Model implements Auditable
{
    use AuditingTrait;

    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'active',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
