<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * AuthController.
 *
 * Отдаёт JSON для AJAX-запросов (модальное окно)
 * и стандартный redirect для обычной отправки формы (graceful fallback).
 */
class AuthController extends Controller
{
    /* ===========================================================
       ВХОД
       =========================================================== */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $data = $this->validateLogin($request);

        $remember = (bool) $request->boolean('remember');

        $ok = Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password']],
            $remember
        );

        if (!$ok) {
            // Не разглашаем, что именно не так (почта или пароль) — стандартная практика
            throw ValidationException::withMessages([
                'email' => ['Не сходится — почта или тайное слово.'],
            ]);
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Для админа отправляем сразу в админку, остальных — на referrer или главную
        $redirect = $user->isAdmin()
            ? route('admin.dashboard')
            : ($request->input('redirect') ?: url()->previous());

        return $this->respond($request, [
            'ok'       => true,
            'message'  => 'Добро пожаловать, ' . $user->name . '.',
            'user'     => $this->userPayload($user),
            'redirect' => $redirect,
        ], $redirect);
    }

    /* ===========================================================
       РЕГИСТРАЦИЯ
       =========================================================== */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $data = $this->validateRegister($request);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // хешируется автоматически (cast 'hashed')
            'role'     => User::ROLE_USER,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->respond($request, [
            'ok'       => true,
            'message'  => 'Записали в книгу, ' . $user->name . '. Кладовая открыта.',
            'user'     => $this->userPayload($user),
            'redirect' => url('/'),
        ], url('/'));
    }

    /* ===========================================================
       ВЫХОД
       =========================================================== */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->respond($request, [
            'ok'       => true,
            'message'  => 'До новой встречи.',
            'redirect' => url('/'),
        ], url('/'));
    }

    /* ===========================================================
       ВАЛИДАЦИЯ
       Сообщения в стиле сайта — фольклор + ясность.
       =========================================================== */

    private function validateLogin(Request $request): array
    {
        return $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ], [
            'email.required'    => 'Введите почту.',
            'email.email'       => 'Похоже, это не почта.',
            'password.required' => 'Введите тайное слово.',
        ]);
    }

    private function validateRegister(Request $request): array
    {
        return $request->validate([
            'name'                  => ['required', 'string', 'min:2', 'max:60'],
            'email'                 => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'password'              => [
                'required',
                'string',
                'min:6',
                'max:120',
                'confirmed',
                'regex:/[A-Za-zА-Яа-яЁё]/', // хотя бы одна буква (рус/лат)
                'regex:/\d/',                // хотя бы одна цифра
            ],
            'agree'                 => ['accepted'],
        ], [
            'name.required'       => 'Без имени в книгу не записать.',
            'name.min'            => 'Имя слишком короткое.',
            'name.max'            => 'Имя слишком длинное.',
            'email.required'      => 'Введите почту.',
            'email.email'         => 'Похоже, это не почта.',
            'email.unique'        => 'Такая почта уже в книге — может, войдёте?',
            'password.required'   => 'Придумайте тайное слово.',
            'password.min'        => 'Тайное слово — не короче 6 знаков.',
            'password.confirmed'  => 'Тайные слова не сходятся.',
            'password.regex'      => 'В тайном слове должны быть и буквы, и цифры.',
            'agree.accepted'      => 'Без согласия записать в книгу не можем.',
        ]);
    }

    /* ===========================================================
       Хелперы
       =========================================================== */

    /** Готовый payload пользователя для JSON-ответа. */
    private function userPayload(User $user): array
    {
        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'role'     => $user->role,
            'is_admin' => $user->isAdmin(),
        ];
    }

    /**
     * Унифицированный ответ.
     * Для AJAX (ожидает JSON) — JSON, для классической формы — redirect.
     */
    private function respond(Request $request, array $payload, string $redirect): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($payload);
        }
        return redirect($redirect)
            ->with('flash', $payload['message'] ?? null);
    }
}
