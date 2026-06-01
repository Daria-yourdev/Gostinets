<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * POST /delivery/save — сохранение выбора способа доставки в сессию
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'mode'   => ['required', 'in:russia,pickup,gift'],
            'method' => ['nullable', 'in:cdek,post,pickup'],
        ]);

        // Для самовывоза всегда method=pickup
        if ($data['mode'] === 'pickup') {
            $data['method'] = 'pickup';
        }

        // Сохраняем в сессию
        session([
            'delivery_choice' => [
                'mode'   => $data['mode'],
                'method' => $data['method'] ?? 'cdek',
            ],
        ]);

        return response()->json([
            'ok'    => true,
            'label' => $this->labelFor($data['mode'], $data['method'] ?? null),
        ]);
    }

    /**
     * Подпись для шапки сайта
     */
    private function labelFor(string $mode, ?string $method): string
    {
        if ($mode === 'pickup' || $method === 'pickup') return 'Самовывоз, Казань';
        if ($mode === 'gift')   return 'В подарок · ' . ($method === 'post' ? 'Почта' : 'СДЭК');

        return $method === 'post' ? 'Почта России' : 'СДЭК';
    }
}
