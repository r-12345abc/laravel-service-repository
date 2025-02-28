<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;

interface OrderRepositoryInterface {
    public function create(array $data, array $itemws): Order;
}
