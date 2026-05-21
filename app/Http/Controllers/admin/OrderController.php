<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * GET /admin/orders — список заказов с фильтрами
     */
    public function index(Request $request)
    {
        $query = Order::with('items')->orderByDesc('created_at');

        // Фильтр по статусу
        if ($request->filled('status') && array_key_exists($request->status, Order::STATUS_LABELS)) {
            $query->where('status', $request->status);
        }

        // Поиск по номеру / email / телефону / имени
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('number', 'like', "%{$q}%")
                  ->orWhere('contact_email', 'like', "%{$q}%")
                  ->orWhere('contact_phone', 'like', "%{$q}%")
                  ->orWhere('contact_name', 'like', "%{$q}%");
            });
        }

        // Считаем счётчики по статусам (для табов)
        $counts = [];
        foreach (array_keys(Order::STATUS_LABELS) as $status) {
            $counts[$status] = Order::where('status', $status)->count();
        }
        $counts['all'] = Order::count();

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', [
            'orders'       => $orders,
            'counts'       => $counts,
            'statusLabels' => Order::STATUS_LABELS,
            'currentStatus' => $request->status,
            'q'            => $request->q,
        ]);
    }

    /**
     * GET /admin/orders/{order} — детали заказа
     */
    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * PATCH /admin/orders/{order}/status — изменить статус
     */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,paid,processing,shipped,delivered,canceled'],
        ]);

        $order->status = $data['status'];

        // Если перевели в canceled — фиксируем время
        if ($data['status'] === 'canceled' && !$order->canceled_at) {
            $order->canceled_at = now();
        }
        // Если перевели в paid вручную — фиксируем оплату
        if ($data['status'] === 'paid' && !$order->paid_at) {
            $order->paid_at = now();
        }

        $order->save();

        return redirect()->route('admin.orders.show', $order)
            ->with('flash', "Статус изменён: {$order->statusLabel()}");
    }
}
