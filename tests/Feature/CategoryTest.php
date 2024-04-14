<?php

namespace Tests\Feature;

use App\Http\Controllers\CategoryController;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $response = $this->get('api/');

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
}
