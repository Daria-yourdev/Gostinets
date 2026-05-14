<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // ЮКасса webhook — приходит без CSRF-токена, и это нормально:
        // защита идёт через сверку payment_id и whitelist IP в самом сервисе.
        'yookassa/webhook',
    ];
}
