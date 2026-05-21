<?php

namespace App\Http\Controllers;

use App\Models\CustomJam;
use App\Models\Order;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * GET /account — личный кабинет (редирект на заказы)
     */
    public function index()
    {
        return redirect()->route('orders');
    }

    /**
     * GET /orders — список заказов пользователя
     */
    public function orders()
    {
        abort_unless(auth()->check(), 403);

        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        $jams = CustomJam::where('user_id', auth()->id())
            ->whereNotIn('status', ['draft'])
            ->orderByDesc('created_at')
            ->get();

        return view('account.orders', compact('orders', 'jams'));
    }
}
