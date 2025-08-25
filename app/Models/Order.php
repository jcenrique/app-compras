<?php

namespace App\Models;

use App\Enum\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'client_id',
        'market_id',
        'order_date',
        'status',

        'notes'
    ];

    public  $casts = [
        'order_date' => 'datetime',
        'status' => OrderStatus::class, // This should match the enum type in your database

    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
