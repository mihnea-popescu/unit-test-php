<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    const ORDER_STATUS_INITIAL = 'initial';
    const ORDER_STATUS_CANCELED = 'canceled';
    const ORDER_STATUS_FINISHED = 'finished';

    const ORDER_STATUS = [
        self::ORDER_STATUS_INITIAL,
        self::ORDER_STATUS_CANCELED,
        self::ORDER_STATUS_FINISHED
    ];

    protected $fillable = [
        'user_id',
        'status'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalPriceAttribute(): float
    {
        $sum = 0;

        foreach ($this->items as $item) {
            $sum += $item->price * $item->quantity;
        }

        return $sum;
    }
}
