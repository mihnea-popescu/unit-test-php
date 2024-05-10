<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        // Update the stock of the item
        if ($product = $orderItem->product) {
            $product->stock -= $orderItem->quantity;
            $product->save();
        }
    }
}
