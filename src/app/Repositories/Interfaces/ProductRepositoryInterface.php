<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;

interface ProductRepositoryInterface {
    public function findById(int $id): ?product;
    public function findByIdWithLock(int $id): ?product;
    public function findAll(): array;
    public function decreaseStock(Product $product, int $quantity): void;
}
