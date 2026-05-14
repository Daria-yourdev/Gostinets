<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRole — middleware для разграничения доступа по ролям.
 *
 * Использование в роутах:
 *   Route::middleware('role:admin')->group(...)
 *   Route::middleware('role:admin,manager')->...
 *
 * Регистрация в App\Http\Kernel::$middlewareAliases:
 *   'role' => \App\Http\Middleware\CheckRole::class,
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Не залогинен
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Сначала войди в избу.',
                ], 401);
            }
            return redirect('/?auth=login');
        }

        $user = Auth::user();

        // Если ролей не передали — пропускаем любого авторизованного
        if (empty($roles)) {
            return $next($request);
        }

        // Проверяем, входит ли роль пользователя в список разрешённых
        if (!in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Сюда — только хозяйка котла.',
                ], 403);
            }
            abort(403, 'Сюда — только хозяйка котла.');
        }

        return $next($request);
    }
}
