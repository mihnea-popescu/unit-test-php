<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders(): void {
        $user = User::inRandomOrder()->first();

        $response = $this->get("api/user/{$user->id}/orders");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->each(fn (AssertableJson $json) =>
                    $json->where('user_id', $user->id)->etc()
                )
            )
            ->assertJsonStructure([
                '*' => [
                    'id', 'user_id', 'status', 'items' => [
                        '*' => [
                            'id', 'order_id', 'product_id', 'quantity', 'price'
                        ]
                    ]
                ]
            ]);
    }
}
