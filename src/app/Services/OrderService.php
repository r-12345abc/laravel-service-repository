<?php

namespace App\Services;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService {
    private ProductRepositoryInterface $productRepository;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    public function order(int $userId, array $items) {
        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $orderItems = [];

            // TODO 商品の占有ロックする順番を商品IDの昇順などに変更する($itemsの順番を並び替え)
            foreach ($items as $item) {
                // 商品を占有ロックして取得
                $product = $this->productRepository->findByIdWithLock($item['product_id']);

                if (!$product) {
                    throw new Exception('商品が見つかりません');
                }

                // 在庫を減らす
                $this->productRepository->decreaseStock($product, $item['quantity']);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];

                $totalPrice += $product->price * $item['quantity'];
            }

            // 注文を作成
            $order = $this->orderRepository->create(
                ['user_id' => $userId, 'total_price' => $totalPrice],
                $orderItems
            );

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
