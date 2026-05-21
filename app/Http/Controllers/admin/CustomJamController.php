<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomJam;
use Illuminate\Http\Request;

class CustomJamController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomJam::with('user')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('label_name', 'like', "%{$q}%")
                  ->orWhere('berry_main', 'like', "%{$q}%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$q}%")
                                                  ->orWhere('name', 'like', "%{$q}%"));
            });
        }

        $jams = $query->paginate(20)->withQueryString();

        $counts = [
            'all'       => CustomJam::count(),
            'draft'     => CustomJam::where('status', 'draft')->count(),
            'ordered'   => CustomJam::where('status', 'ordered')->count(),
            'cooking'   => CustomJam::where('status', 'cooking')->count(),
            'ready'     => CustomJam::where('status', 'ready')->count(),
            'delivered' => CustomJam::where('status', 'delivered')->count(),
        ];

        return view('admin.custom-jams.index', [
            'jams'   => $jams,
            'counts' => $counts,
            'q'      => $request->q,
            'currentStatus' => $request->status,
        ]);
    }

    public function show(CustomJam $customJam)
    {
        $customJam->load('user');
        return view('admin.custom-jams.show', ['jam' => $customJam]);
    }

    public function updateStatus(Request $request, CustomJam $customJam)
    {
        $data = $request->validate([
            'status' => ['required', 'in:draft,ordered,cooking,ready,delivered'],
        ]);

        $customJam->update(['status' => $data['status']]);

        return redirect()->route('admin.custom-jams.show', $customJam)
            ->with('flash', 'Статус котелка обновлён');
    }
}
