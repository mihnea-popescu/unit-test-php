<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Order::all() as $order) {
            OrderItem::factory()->count(rand(1, 4))->create([
                'order_id' => $order->id
            ]);
        }
    }
}
