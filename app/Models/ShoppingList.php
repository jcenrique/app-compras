<?php

namespace App\Models;

use App\Enum\StatusShopping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ShoppingList extends Model
{
    /** @use HasFactory<\Database\Factories\ShoppingListFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_date',
        'user_id',
        'supermarket_id',
        'description',
        'status'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusShopping::class,
        ];
    }

    public function supermarket() :BelongsTo {
        return $this->belongsTo(Supermarket::class);
    }

    public function user() :BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function listItems() : HasMany
    {
        return $this->hasMany(ListItem::class);
        

    }

   
    
}
