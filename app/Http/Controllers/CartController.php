<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    /**
     * GET /cart — страница корзины
     */
    public function index()
    {
        $customItems = $this->cart->customItems();
        $customsSum  = $customItems->sum('subtotal');

        return view('cart', [
            'items'       => $this->cart->items(),
            'customItems' => $customItems,
            'subtotal'    => $this->cart->subtotal() + $customsSum,  // ← теперь с кастомами
            'count'       => $this->cart->count(),
        ]);
    }

    /**
     * POST /cart/add — добавить товар (AJAX)
     */
    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty'        => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $qty = $this->cart->add($data['product_id'], $data['qty'] ?? 1);

        return response()->json([
            'ok'    => $qty > 0,
            'qty'   => $qty,
            'count' => $this->cart->count(),
            'message' => $qty > 0
                ? 'Положено в мешочек'
                : 'Не вышло — банка закончилась',
        ]);
    }

    /**
     * PATCH /cart/{product} — изменить количество (AJAX)
     */
    public function update(Request $request, int $productId)
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $qty = $this->cart->setQty($productId, $data['qty']);

        return response()->json([
            'ok'       => true,
            'qty'      => $qty,
            'count'    => $this->cart->count(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    /**
     * DELETE /cart/{product} — удалить позицию (AJAX)
     */
    public function destroy(int $productId)
    {
        $this->cart->remove($productId);

        return response()->json([
            'ok'       => true,
            'count'    => $this->cart->count(),
            'subtotal' => $this->cart->subtotal(),
            'empty'    => $this->cart->isEmpty(),
        ]);
    }

    /**
     * DELETE /cart/custom/{customJam} — удалить кастомный котёл (AJAX)
     */
    public function destroyCustom(int $customJamId)
    {
        $this->cart->removeCustom($customJamId);

        return response()->json([
            'ok'       => true,
            'count'    => $this->cart->count(),
            'subtotal' => $this->cart->subtotal(),
            'empty'    => $this->cart->isEmpty(),
        ]);
    }
}
