<?php

namespace Tests\Feature;

use App\Exception\InvalidQuantityException;
use App\Exception\ProductNotFoundException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Order\OrderCreator;
use App\Order\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
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

    public function test_order_store(): void
    {
        $user = User::inRandomOrder()->first();

        $totalProducts = Product::query()
            ->where('stock', '>', 0)
            ->count();
        $productsCount = fake()->numberBetween(1, $totalProducts);

        /** @var Collection<Product> $products */
        $products = Product::query()
            ->inRandomOrder()
            ->limit($productsCount)
            ->get();

        $creator = app('order.creator');
        $order = $creator->create(
            $user->id,
            $products
                ->map(function(Product $product) {
                    $quantity = fake()->numberBetween(1, $product->stock);
                    return new OrderItem($product->id, $quantity);
                })
                ->all()
        );

        $this->assertTrue($order->id > 0);
    }

    public function test_order_store_invalid_products(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $user = User::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();

        $creator = app('order.creator');
        $creator->create(
            $user->id,
            [
                new OrderItem($product->id, 1),
                new OrderItem(PHP_INT_MAX, 1)
            ]
        );
    }

    public function test_order_store_invalid_quantity(): void
    {
        $this->expectException(InvalidQuantityException::class);

        $user = User::inRandomOrder()->first();

        $totalProducts = Product::query()
            ->where('stock', '>', 0)
            ->count();
        $productsCount = fake()->numberBetween(1, $totalProducts);

        /** @var Collection<Product> $products */
        $products = Product::query()
            ->inRandomOrder()
            ->limit($productsCount)
            ->get();

        $creator = app('order.creator');
        $creator->create(
            $user->id,
            $products
                ->map(function(Product $product) {
                    $quantity = fake()->numberBetween($product->stock + 1, PHP_INT_MAX);
                    return new OrderItem($product->id, $quantity);
                })
                ->all()
        );
    }
}
