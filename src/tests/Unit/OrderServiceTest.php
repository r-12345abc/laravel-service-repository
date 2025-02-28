<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Services\OrderService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;
    private ProductRepository $productRepository;
    private OrderRepository $orderRepository;
    private User $user;

    protected function setUp(): void {
        parent::setUp();
        $this->productRepository = new ProductRepository();
        $this->orderRepository = new OrderRepository();
        $this->orderService = new OrderService($this->productRepository, $this->orderRepository);

        $this->user = User::factory()->create();
    }

    public function test_商品を正常に注文できる() {
        // 商品を作成
        $product1 = Product::factory()->create(['stock' => 10, 'price' => 1000]);
        $product2 = Product::factory()->create(['stock' => 5, 'price' => 2000]);

        $cartItems = [
            ['product_id' => $product1->id, 'quantity' => 2], // 1000円 × 2
            ['product_id' => $product2->id, 'quantity' => 1]  // 2000円 × 1
        ];

        // 注文を作成
        $order = $this->orderService->order($this->user->id, $cartItems);

        // 注文がされたことを確認
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'total_price' => 4000,
        ]);

        // 注文詳細が正しく保存されているか確認
        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 1000,
        ]);

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 2000,
        ]);

        // 在庫が正しく減っているか確認
        $this->assertEquals(8, $product1->refresh()->stock); // 10 - 2
        $this->assertEquals(4, $product2->refresh()->stock); // 5 - 1
    }

    public function test_在庫不足の注文はロールバックされる() {
        // 商品を作成
        $product1 = Product::factory()->create(['stock' => 3, 'price' => 1000]);
        $product2 = Product::factory()->create(['stock' => 2, 'price' => 2000]);

        // カート（在庫不足）
        $cartItems = [
            ['product_id' => $product1->id, 'quantity' => 2], // 1000円 × 2
            ['product_id' => $product2->id, 'quantity' => 3]  // 在庫2しかないのに 3 を注文
        ];

        // 在庫不足エラーを確認
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('在庫が不足しています');

        // 注文処理（例外発生）
        $this->orderService->order($this->user->id, $cartItems);

        // 注文が作成されていないことを確認
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);

        // 在庫が変わっていないことを確認
        $this->assertEquals(3, $product1->refresh()->stock);
        $this->assertEquals(2, $product2->refresh()->stock);
    }
}
