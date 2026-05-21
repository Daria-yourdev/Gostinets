<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Куда редиректить гостя при попытке зайти на защищённую страницу.
     * Поскольку /login — это POST (форма в модалке), редиректим
     * на главную с параметром, который откроет auth-модалку.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : url('/?auth=login');
    }
}