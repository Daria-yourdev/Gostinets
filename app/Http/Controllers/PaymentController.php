<?php

namespace App\Http\Controllers;

use App\Services\YooKassaService;
use Illuminate\Http\Request;

/**
 * PaymentController — webhook от ЮКассы.
 * URL: POST /yookassa/webhook
 */
class PaymentController extends Controller
{
    /**
     * Официальный список IP-адресов ЮКассы.
     * https://yookassa.ru/developers/using-api/webhooks#ip
     */
    private const YOOKASSA_IPS = [
        '185.71.76.0/27',
        '185.71.77.0/27',
        '77.75.153.0/25',
        '77.75.154.128/25',
        '77.75.156.11',
        '77.75.156.35',
        '2a02:5180::/32',
    ];

    public function __construct(private YooKassaService $yookassa) {}

    public function webhook(Request $request)
    {
        if (!$this->isYookassaIp($request->ip())) {
            \Log::warning('YooKassa webhook: подозрительный IP', ['ip' => $request->ip()]);
            return response('forbidden', 403);
        }

        $this->yookassa->handleWebhook($request->all());

        return response('', 200);
    }

    private function isYookassaIp(string $ip): bool
    {
        if (app()->environment('local')) {
            return true;
        }

        foreach (self::YOOKASSA_IPS as $range) {
            if (str_contains($range, '/')) {
                if ($this->ipInRange($ip, $range)) return true;
            } else {
                if ($ip === $range) return true;
            }
        }
        return false;
    }

    private function ipInRange(string $ip, string $range): bool
    {
        [$subnet, $bits] = explode('/', $range);

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipLong  = ip2long($ip);
            $subLong = ip2long($subnet);
            if ($ipLong === false || $subLong === false) return false;
            $mask = -1 << (32 - (int) $bits);
            return ($ipLong & $mask) === ($subLong & $mask);
        }

        return str_starts_with(inet_pton($ip) ?: '', inet_pton($subnet) ?: '');
    }
}