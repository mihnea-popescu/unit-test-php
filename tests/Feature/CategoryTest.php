<?php

namespace Tests\Feature;

use App\Http\Controllers\CategoryController;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Categories exist
     */
    public function test_categories_exist(): void
    {
        $this->assertDatabaseCount('categories', 4);
    }

    /**
     * Get categories list with their products as well
     */
    public function test_get_categories_list(): void
    {
        $response = $this->get(route('category.list'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id', 'name', 'products' => [
                    '*' => [
                        'id', 'name', 'price', 'sale_price'
                    ]
                ]
            ]
        ]);
        $response->assertJsonCount(4);
    }

    public function test_get_category(): void
    {
        $categ = Category::inRandomOrder()->first();

        $response = $this->get(route('category.show', ['category' => $categ->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'products' => [
                    '*' => [
                        'id', 'name', 'price', 'sale_price'
                    ]
                ]
            ])
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('id', $categ->id)
                    ->where('name', $categ->name)
                    ->etc()
            );
    }
}
