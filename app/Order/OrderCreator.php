<?php

namespace App\Order;

use App\Exception\InvalidQuantityException;
use App\Exception\ProductNotFoundException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use App\Models\OrderItem as OrderItemModel;
use Illuminate\Support\Collection;

class OrderCreator {

    /**
     * @param int $userId The user creating the order.
     * @param array<OrderItem> $items Order items.
     *
     * @return Order The created order.
     *
     * @throws Exception
     */
    public function create(
        int $userId,
        array $items
    ): Order {
        $user = User::find($userId);
        if(!$user) {
            throw new Exception("User not found");
        }

        $items = collect($items)
            ->keyBy(static fn(OrderItem $item) => $item->productId);

        $productIds = $items->keys();

        /** @var Collection<Product> $products */
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy(static fn(Product $item) => $item->id);

        // Make sure all products exist.
        if($products->count() != count($productIds)) {
            throw new ProductNotFoundException('Products not found');
        }

        // Validate the stocks.
        foreach($items as $item) {
            /** @var OrderItem $item */
            $dbStock = $products->get($item->productId)->stock;
            if($dbStock < $item->quantity) {
                throw new InvalidQuantityException('Product out of stock');
            }
        }

        // Create the order.
        /** @var Order $order */
        $order = Order::query()->create([
            'user_id' => $userId,
            'status' => Order::ORDER_STATUS_INITIAL
        ]);

        foreach($items as $item) {
            $product = $products->get($item->productId);

            OrderItemModel::query()->create([
                'order_id' => $order->id,
                'product_id' => $item->productId,
                'quantity' => $item->quantity,
                'price' => $product->price
            ]);
        }

        return $order;
    }

}
