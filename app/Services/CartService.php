<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * CartService — корзина в сессии Laravel.
 *
 * Хранит только product_id => qty в сессии. Все остальные данные
 * (цена, имя, картинка) берутся из БД при чтении — это даёт всегда
 * актуальную цену и защищает от подмены.
 *
 * Структура session('cart'):
 *   [
 *     17 => 2,   // product_id => qty
 *     23 => 1,
 *   ]
 */
class CartService
{
    private const KEY = 'cart';
    private const MAX_QTY_PER_ITEM = 99;

    /* ===========================================================
       ОПЕРАЦИИ
       =========================================================== */

    /** Добавить продукт в корзину. Если уже есть — прибавляет qty. */
    public function add(int $productId, int $qty = 1): int
    {
        $product = Product::active()->find($productId);
        if (!$product) {
            return 0;
        }

        $cart = $this->raw();
        $current = $cart[$productId] ?? 0;
        $newQty  = min(self::MAX_QTY_PER_ITEM, max(1, $current + $qty));
        $newQty  = min($newQty, $product->stock); // не больше остатка

        $cart[$productId] = $newQty;
        Session::put(self::KEY, $cart);

        return $newQty;
    }

    /** Установить точное количество. qty <= 0 — удалить позицию. */
    public function setQty(int $productId, int $qty): int
    {
        $cart = $this->raw();
        if ($qty <= 0) {
            unset($cart[$productId]);
            Session::put(self::KEY, $cart);
            return 0;
        }

        $product = Product::active()->find($productId);
        if (!$product) return 0;

        $qty = min(self::MAX_QTY_PER_ITEM, $qty, $product->stock);
        $cart[$productId] = $qty;
        Session::put(self::KEY, $cart);

        return $qty;
    }

    /** Удалить позицию. */
    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        Session::put(self::KEY, $cart);
    }

    /** Полностью очистить корзину (вызывается после успешной оплаты). */
    public function clear(): void
    {
        Session::forget(self::KEY);
    }

    /* ===========================================================
       ЧТЕНИЕ
       =========================================================== */

    /** Сырой массив product_id => qty. */
    public function raw(): array
    {
        return (array) Session::get(self::KEY, []);
    }

    /** Кол-во позиций (для счётчика в шапке). */
    public function count(): int
    {
        return array_sum($this->raw());
    }

    /** Кол-во уникальных банок. */
    public function uniqueCount(): int
    {
        return count($this->raw());
    }

    /** Корзина пустая? */
    public function isEmpty(): bool
    {
        return empty($this->raw());
    }

    /**
     * Список позиций с подтянутыми из БД продуктами.
     * Каждая позиция: ['product' => Product, 'qty' => int, 'subtotal' => int]
     */
    public function items(): Collection
    {
        $raw = $this->raw();
        if (empty($raw)) return collect();

        $products = Product::active()
            ->whereIn('id', array_keys($raw))
            ->get()
            ->keyBy('id');

        // Чистим из сессии те ID, которых уже нет в каталоге (стали неактивны)
        $stale = array_diff(array_keys($raw), $products->keys()->all());
        if ($stale) {
            foreach ($stale as $id) unset($raw[$id]);
            Session::put(self::KEY, $raw);
        }

        return collect($raw)->map(function ($qty, $id) use ($products) {
            $product = $products[$id];
            return [
                'product'  => $product,
                'qty'      => (int) $qty,
                'subtotal' => $product->price * (int) $qty,
            ];
        })->values();
    }

    /** Сумма всех позиций. */
    public function subtotal(): int
    {
        return $this->items()->sum('subtotal');
    }

    /**
     * Подсчёт стоимости доставки.
     * Если subtotal > 3000 ₽ — СДЭК и Почта бесплатно.
     */
    public function deliveryCost(string $method): int
    {
        if ($this->subtotal() >= 3000) return 0;
        return \App\Models\Order::DELIVERY_METHODS[$method]['cost'] ?? 0;
    }

    /** Итог: subtotal + доставка - скидка. */
    public function total(string $deliveryMethod = 'cdek', int $discount = 0): int
    {
        return max(0, $this->subtotal() + $this->deliveryCost($deliveryMethod) - $discount);
    }
}
