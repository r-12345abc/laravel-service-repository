<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Exception;

class ProductRepository implements ProductRepositoryInterface {
    public function findById(int $id): ?Product {
        return Product::find($id);
    }

    public function findByIdWithLock(int $id): ?Product {
        return Product::where('id', $id)->lockForUpdate()->first();
    }

    public function findAll(): array {
        return Product::all()->toArray();
    }

    public function decreaseStock(Product $product, int $quantity): void {
        if ($product->stock < $quantity) {
            throw new Exception('在庫が不足しています');
        }

        $product->stock -= $quantity;
        $product->save();
    }
}
