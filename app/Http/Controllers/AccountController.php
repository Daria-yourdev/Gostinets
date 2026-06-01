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

    public function profile()
    {
        return view('account.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:60'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'delivery_city'    => ['nullable', 'string', 'max:80'],
            'delivery_zip'     => ['nullable', 'string', 'max:10'],
            'delivery_address' => ['nullable', 'string', 'max:250'],
            'delivery_note'    => ['nullable', 'string', 'max:250'],
        ]);

        auth()->user()->update($data);

        return back()->with('flash', 'Данные сохранены.');
    }
}
