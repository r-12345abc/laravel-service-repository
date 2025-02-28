<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Repositories\Eloquent\ProductRepository;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $productRepository;

    protected function setUp(): void {
        parent::setUp();
        $this->productRepository = new ProductRepository();
    }

    public function test_商品の在庫を正常に減らせる(): void {
        $product = Product::factory()->create(['stock' => 10]);

        // 在庫を5減らす
        $this->productRepository->decreaseStock($product, 5);

        // 在庫が5になっていることを確認
        $this->assertEquals(5, $product->refresh()->stock);
    }

    public function test_在庫不足の場合は例外を投げる(): void {
        $product = Product::factory()->create(['stock' => 2]);

        // 在庫より多い3を減らそうとする
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('在庫が不足しています');
        $this->productRepository->decreaseStock($product, 3);

        // 在庫が変わっていないことを確認
        $this->assertEquals(2, $product->refresh()->stock);
    }
}
