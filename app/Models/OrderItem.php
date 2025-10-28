<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class OrderItem extends Model implements Auditable
{


    use AuditingTrait;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'is_basket'
    ];

    protected $casts = [
        'is_basket' => 'boolean',
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
