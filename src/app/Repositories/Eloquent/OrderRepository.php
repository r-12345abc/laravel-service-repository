<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface {
    public function create(array $data, array $items): Order {
        // 注文を作成
        $order = Order::create($data);

        // 注文明細を作成
        foreach ($items as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return $order;
    }
}
