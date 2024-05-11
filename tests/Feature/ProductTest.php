<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Validation\ValidationException;
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
     * Get a product
     */
    public function test_get_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('product.show', ['product' => $product->id]));

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json
                ->where('id', $product->id)
                ->where('category_id', $product->category_id)
                ->where('name', $product->name)
                ->where('description', $product->description)
                ->where('stock', $product->stock)
                ->where('price', $product->price)
                ->where('sale_price', $product->sale_price)
                ->etc()
        );
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

    public function test_product_get_price_sale_price_higher(): void
    {
        $product = Product::factory()->create();

        $product->sale_price = 10;
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

    public function test_product_update(): void
    {
        $product = Product::factory()->create();

        $newDetails = [
            'category_id' => Category::inRandomOrder()->first()->id,
            'name' => 'Product test #1',
            'description' => 'Product description test #1',
            'stock' => 15,
            'price' => 14.93,
            'sale_price' => 13.94,
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('category_id', $newDetails['category_id'])
                    ->where('name', $newDetails['name'])
                    ->where('description', $newDetails['description'])
                    ->where('stock', $newDetails['stock'])
                    ->where('price', $newDetails['price'])
                    ->where('sale_price', $newDetails['sale_price'])
                    ->etc()
            );
    }

    public function test_product_update_error_validation(): void
    {
        $this->expectException(ValidationException::class);

        $product = Product::factory()->create();

        $newDetails = [
            'category_id' => -1,
            'name' => null,
            'description' => '#',
            'stock' => -23,
            'price' => -23.23232,
            'sale_price' => -0.000001,
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(302)
            ->assertJsonValidationErrors([
                'category_id',
                'name',
                'description',
                'stock',
                'price',
                'sale_price'
            ]);
    }

    public function test_product_update_category_exists(): void
    {
        $this->expectException(ValidationException::class);

        $product = Product::factory()->create();

        $newDetails = [
            'category_id' => 99999999999999999999,
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(302)
            ->assertJsonValidationErrors([
                'category_id',
            ]);
    }

    public function test_product_update_name_string(): void
    {
        $this->expectException(ValidationException::class);

        $product = Product::factory()->create();

        $newDetails = [
            'name' => 23,
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(302)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_product_update_description_nullable(): void
    {
        $product = Product::factory()->create();

        $newDetails = [
            'description' => null,
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(200);

        $this->assertModelExists(Product::where('id', $product->id)->whereNull('description')->first());
    }

    public function test_product_update_stock_price_sale_price_integer(): void
    {
        $this->expectException(ValidationException::class);

        $product = Product::factory()->create();

        $newDetails = [
            'stock' => 23.23,
            'price' => 23.23,
            'sale_price' => 23.24,
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(302)
            ->assertJsonValidationErrors(['stock', 'price', 'sale_price']);


        $newDetails = [
            'stock' => "0x539",
            'price' => "0x539",
            'sale_price' => "0x539",
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(302)
            ->assertJsonValidationErrors(['stock', 'price', 'sale_price']);
    }

    public function test_product_sale_price_nullable(): void
    {
        $product = Product::factory()->create();

        $newDetails = [
            'sale_price' => null,
        ];

        $response = $this->patch(route('product.update', ['product' => $product->id]), $newDetails);

        $response->assertStatus(200);

        $this->assertModelExists(Product::where('id', $product->id)->whereNull('sale_price')->first());
    }
}
