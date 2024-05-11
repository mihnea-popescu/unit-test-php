<?php

namespace Tests\Feature;

use App\Exception\InvalidQuantityException;
use App\Exception\ProductNotFoundException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Order\OrderCreator;
use App\Order\OrderItem;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Validation\ValidationException;
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
                ->map(function (Product $product) {
                    $quantity = fake()->numberBetween(1, $product->stock);
                    return new OrderItem($product->id, $quantity);
                })
                ->all()
        );

        $this->assertTrue($order->id > 0);
        $this->assertTrue($order->items()->count() == $products->count());
    }

    public function test_order_store_product_quantity_equals_stock(): void
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
                ->map(function (Product $product) {
                    return new OrderItem($product->id, $product->stock);
                })
                ->all()
        );

        $this->assertModelExists($order);
    }

    public function test_order_store_invalid_user_id(): void
    {
        $this->expectException(Exception::class);

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
            -1, // Invalid user id
            $products
                ->map(function (Product $product) {
                    $quantity = fake()->numberBetween(1, $product->stock);
                    return new OrderItem($product->id, $quantity);
                })
                ->all()
        );
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
                ->map(function (Product $product) {
                    $quantity = fake()->numberBetween($product->stock + 1, PHP_INT_MAX);
                    return new OrderItem($product->id, $quantity);
                })
                ->all()
        );
    }

    public function test_get_order(): void
    {
        $order = Order::inRandomOrder()->first();

        $response = $this->get(route('order.show', ['order' => $order->id]));

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('user_id', $order->user_id)
                    ->where('id', $order->id)
                    ->where('status', $order->status)
                    ->etc()
            );
    }

    public function test_update_order(): void
    {
        $order = Order::inRandomOrder()->first();

        $data = [
            'status' => Order::ORDER_STATUS_FINISHED
        ];

        $response = $this->patch(route('order.update', ['order' => $order->id]), $data);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)->etc()
            );

        // Verify the model has been updated in the db
        $order->refresh();

        $this->assertTrue($order->status == $data['status']);
    }

    public function test_update_order_initial_new_status_canceled(): void
    {
        $order = Order::inRandomOrder()->where('status', Order::ORDER_STATUS_INITIAL)->first();

        $data = [
            'status' => Order::ORDER_STATUS_CANCELED
        ];

        $response = $this->patch(route('order.update', ['order' => $order->id]), $data);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)->etc()
            );

        // Verify the model has been updated in the db
        $order->refresh();

        $this->assertTrue($order->status == $data['status']);
    }

    public function test_update_order_error_no_data(): void
    {
        $this->expectException(ValidationException::class);

        $order = Order::inRandomOrder()->first();

        $response = $this->patch(route('order.update', ['order' => $order->id]), []);

        // Because user is not authenticated
        $response->assertStatus(302)
            ->assertJsonValidationErrorFor('status');
    }

    public function test_update_order_error_finished_new_status_canceled(): void
    {
        $order = Order::inRandomOrder()->where('status', Order::ORDER_STATUS_FINISHED)->first();
        $data = [
            'status' => Order::ORDER_STATUS_CANCELED,
        ];

        $response = $this->patch(route('order.update', ['order' => $order->id]), $data);

        $response->assertStatus(400)
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)->where('error', 'Invalid status change.')->etc()
            );
    }

    public function test_update_order_error_finished_new_status_initial(): void
    {
        // Test for order status finished
        $order = Order::inRandomOrder()->where('status', Order::ORDER_STATUS_FINISHED)->first();
        $data = [
            'status' => Order::ORDER_STATUS_INITIAL,
        ];

        $response = $this->patch(route('order.update', ['order' => $order->id]), $data);

        $response->assertStatus(400)->assertJson(
            fn (AssertableJson $json) => $json->where('success', false)->where('error', 'Invalid status change.')->etc()
        );

        // Test for order status canceled
        $order = Order::inRandomOrder()->where('status', Order::ORDER_STATUS_CANCELED)->first();
        $data = [
            'status' => Order::ORDER_STATUS_INITIAL,
        ];

        $response = $this->patch(route('order.update', ['order' => $order->id]), $data);

        $response->assertStatus(400)->assertJson(
            fn (AssertableJson $json) => $json->where('success', false)->where('error', 'Invalid status change.')->etc()
        );
    }
}
