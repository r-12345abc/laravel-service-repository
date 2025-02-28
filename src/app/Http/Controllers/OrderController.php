<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    public function order(Request $request) {
        // TODO フォームリクエストに移す
        $request->validate([
            'items'   => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity'   => 'required|integer|min:1'
        ]);

        try {
            $order = $this->orderService->order(
                auth()->user()->id,
                $request->items
            );

            return view('front.order.complete', compact('order'));
        } catch (Exception $e) {
            // TODO ログ出力
            abort(500);
        }
    }
}
