<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')->orderByDesc('created_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'q'     => $request->q,
            'role'  => $request->role,
        ]);
    }

    public function show(User $user)
    {
        $user->load(['orders' => function ($q) {
            $q->orderByDesc('created_at')->take(50);
        }]);

        $stats = [
            'orders_count'   => $user->orders()->count(),
            'orders_paid'    => $user->orders()->whereIn('status', ['paid','processing','shipped','delivered'])->count(),
            'total_spent'    => $user->orders()->whereIn('status', ['paid','processing','shipped','delivered'])->sum('total'),
            'customs_count'  => $user->customJams()->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        // Не даём админу разжаловать самого себя
        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Себя разжаловать нельзя.']);
        }

        $user->update(['role' => $data['role']]);

        return back()->with('flash',
            "Роль для «{$user->name}» изменена на «{$data['role']}»"
        );
    }
}
