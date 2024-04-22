<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test that the order price total price attribute is correct
     */
    public function test_order_total_price(): void
    {
        $order = Order::inRandomOrder()->first();

        $total = $order->totalPrice;

        $calculatedTotal = 0;

        foreach ($order->items as $item) {
            $calculatedTotal += $item->price * $item->quantity;
        }

        $this->assertTrue($total == $calculatedTotal);
    }
}
