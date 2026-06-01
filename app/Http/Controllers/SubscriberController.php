<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * POST /subscribe — подписка через AJAX
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email'  => ['required', 'email', 'max:160'],
            'name'   => ['nullable', 'string', 'max:80'],
            'source' => ['nullable', 'string', 'max:40'],
        ], [
            'email.required' => 'Укажи почту — куда слать весточки.',
            'email.email'    => 'Это не похоже на адрес почты.',
        ]);

        $email = mb_strtolower(trim($data['email']));

        // Уже подписан?
        if (Subscriber::where('email', $email)->exists()) {
            return response()->json([
                'ok'      => true,
                'already' => true,
                'message' => 'Ты уже в списке. Весточки придут.',
            ]);
        }

        Subscriber::create([
            'email'  => $email,
            'name'   => $data['name'] ?? null,
            'source' => $data['source'] ?? 'footer',
        ]);

        return response()->json([
            'ok'      => true,
            'already' => false,
            'message' => 'Добавили! Будем писать только о важном.',
        ]);
    }
}