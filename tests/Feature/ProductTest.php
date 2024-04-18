<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * A product can be created
     */
    public function test_create_product(): void
    {
        $product = Product::factory()->create();

        $this->assertModelExists($product);
    }

    /**
     * Test product's sale price
     */
    public function test_product_get_sale_price(): void
    {
        $product = Product::factory()->create();

        $product->sale_price = 2.5;
        $product->price = 5;

        $this->assertTrue($product->getPrice() == 2.5);
    }

    /**
     * Test product's regular price
     */
    public function test_product_get_price(): void
    {
        $product = Product::factory()->create();

        $product->sale_price = null;
        $product->price = 5;

        $this->assertTrue($product->getPrice() == 5);
    }

    /**
     * Test products updating stock
     * (Stock update is in OrderItemObserver)
     */
    public function test_product_stock_update(): void
    {
        $order = Order::factory()->create([
            'user_id' => User::inRandomOrder()->first()->id
        ]);

        $quantity = rand(1, 10);

        $product = Product::factory()->create([
            'stock' => $quantity += 10,
        ]);

        $previousQuantity = $product->stock;

        $orderItem = new OrderItem([
            'product_id' => $product->id,
            'price' => $product->getPrice(),
            'quantity' => $quantity,
        ]);

        $order->items()->save($orderItem);

        $product->refresh();

        $this->assertTrue($previousQuantity == ($orderItem->quantity + $product->stock));
    }
}
