<?php

namespace App\Http\Controllers;

use App\Services\YooKassaService;
use Illuminate\Http\Request;

/**
 * PaymentController — webhook от ЮКассы.
 *
 * URL: POST /yookassa/webhook
 *
 * Этот endpoint нужно прописать в личном кабинете ЮКассы:
 *   Интеграция → HTTP-уведомления → URL для уведомлений
 *
 * Webhook исключён из CSRF — см. VerifyCsrfToken::$except.
 * ЮКасса требует ответ 200 OK, иначе будет повторять попытки.
 */
class PaymentController extends Controller
{
    public function __construct(private YooKassaService $yookassa) {}

    /**
     * POST /yookassa/webhook
     */
    public function webhook(Request $request)
    {
        // Опционально: проверить, что IP в whitelist ЮКассы
        // https://yookassa.ru/developers/using-api/webhooks#ip
        // if (!$this->isYookassaIp($request->ip())) {
        //     return response('forbidden', 403);
        // }

        $payload = $request->all();

        // Обрабатываем; даже если что-то не сошлось — отвечаем 200,
        // чтобы ЮКасса не долбила повторами. Ошибки логируются в сервисе.
        $this->yookassa->handleWebhook($payload);

        return response('', 200);
    }
}
