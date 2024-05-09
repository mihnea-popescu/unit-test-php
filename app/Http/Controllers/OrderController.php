<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        return $order;
    }

    /**
     * Update the specified order in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $newStatus = $request->get('status');

        // Do not allow an order to go back in status
        if ($newStatus === Order::ORDER_STATUS_INITIAL && in_array($order->status, [Order::ORDER_STATUS_CANCELED, Order::ORDER_STATUS_FINISHED]) || $newStatus === Order::ORDER_STATUS_CANCELED && $order->status === Order::ORDER_STATUS_FINISHED) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid status change.'
            ], 400);
        }

        $order->status = $newStatus;
        $order->save();

        return [
            'success' => true
        ];
    }
}
