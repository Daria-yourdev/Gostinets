<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomJam;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

/**
 * DashboardController — обзорный экран админки.
 * Считает основные метрики магазина.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $weekAgo   = Carbon::now()->subDays(7);
        $monthAgo  = Carbon::now()->subDays(30);

        // Метрики заказов
        $ordersToday   = Order::whereDate('created_at', $today)->count();
        $ordersWeek    = Order::where('created_at', '>=', $weekAgo)->count();
        $ordersPending = Order::where('status', 'pending')->count();
        $ordersPaid    = Order::where('status', 'paid')->count();

        // Выручка — только оплаченные заказы
        $revenueToday = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->whereDate('paid_at', $today)
            ->sum('total');

        $revenueMonth = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->where('paid_at', '>=', $monthAgo)
            ->sum('total');

        // Топ-5 товаров по продажам за месяц
        $topProducts = \DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['paid', 'processing', 'shipped', 'delivered'])
            ->where('orders.paid_at', '>=', $monthAgo)
            ->select('order_items.product_id', 'order_items.product_name',
                     \DB::raw('SUM(order_items.qty) as total_qty'),
                     \DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Последние заказы
        $latestOrders = Order::with('items')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Прочие счётчики
        $productsTotal  = Product::count();
        $productsActive = Product::where('is_active', true)->count();
        $usersTotal     = User::count();
        $customsPending = CustomJam::where('status', 'ordered')->count();

        // Низкий остаток — товары с stock < 5
        $lowStock = Product::where('is_active', true)
            ->where('stock', '<', 5)
            ->orderBy('stock')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'ordersToday', 'ordersWeek', 'ordersPending', 'ordersPaid',
            'revenueToday', 'revenueMonth',
            'topProducts', 'latestOrders',
            'productsTotal', 'productsActive',
            'usersTotal', 'customsPending',
            'lowStock'
        ));
    }
}
