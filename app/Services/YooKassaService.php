<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationFactory;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationCanceled;

/**
 * YooKassaService — обёртка над официальным SDK ЮKassa.
 *
 * Документация:
 *   https://yookassa.ru/developers/api
 *   https://github.com/yoomoney/yookassa-sdk-php
 *
 * Тестовый режим:
 *   1. В личном кабинете → «Интеграция» → включить «Тестовый магазин»
 *   2. Получить test shop_id и test secret_key
 *   3. Прописать в .env (см. YOOKASSA_TEST=true)
 *
 * Тестовые карты:
 *   5555 5555 5555 4444 — успешная оплата
 *   5555 5555 5555 4477 — отказ от платежа
 */
class YooKassaService
{
    private Client $client;
    private bool $configured;

    public function __construct()
    {
        $shopId    = config('services.yookassa.shop_id');
        $secretKey = config('services.yookassa.secret_key');

        // Если конфиг не задан — клиент работает «вхолостую»
        // и при попытке оплаты вернёт null с понятной ошибкой
        $this->configured = !empty($shopId) && !empty($secretKey);

        $this->client = new Client();

        if ($this->configured) {
            $this->client->setAuth(
                (int) $shopId,           // ← было (string), теперь (int) — SDK требует int
                (string) $secretKey,
            );
        }
    }

    /**
     * Создать платёж в ЮКассе и сохранить ID в заказе.
     * Возвращает URL, на который надо редиректить пользователя.
     */
    public function createPayment(Order $order): ?string
    {
        if (!$this->configured) {
            Log::error('YooKassa не настроена: проверь YOOKASSA_SHOP_ID и YOOKASSA_SECRET_KEY в .env');
            return null;
        }

        try {
            $idempotenceKey = uniqid('gostinec_' . $order->id . '_', true);

            $payment = $this->client->createPayment([
                'amount' => [
                    'value'    => number_format($order->total, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type'       => 'redirect',
                    'return_url' => route('checkout.return', $order),
                ],
                'capture'     => true,
                'description' => "Заказ {$order->number} — Гостинецъ",
                'metadata'    => [
                    'order_id'     => $order->id,
                    'order_number' => $order->number,
                ],
                // Чек для самозанятых / ИП — опционально, можно включить позже
                // 'receipt' => [
                //     'customer' => [
                //         'email' => $order->contact_email,
                //         'phone' => $order->contact_phone,
                //     ],
                //     'items' => $this->buildReceiptItems($order),
                // ],
            ], $idempotenceKey);

            // Сохраняем ID платежа в заказ
            $order->update([
                'yookassa_payment_id' => $payment->getId(),
                'yookassa_status'     => $payment->getStatus(),
            ]);

            return $payment->getConfirmation()->getConfirmationUrl();
        } catch (\Throwable $e) {
            Log::error('YooKassa createPayment failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Обработать webhook от ЮКассы.
     * Возвращает true, если событие обработано (Order найден и обновлён).
     */
    public function handleWebhook(array $payload): bool
    {
        try {
            $factory = new NotificationFactory();
            $notification = $factory->factory($payload);

            $paymentResponse = $notification->getObject();
            $paymentId = $paymentResponse->getId();
            $metadata = $paymentResponse->getMetadata();
            $orderId = $metadata?->order_id ?? null;

            if (!$orderId) {
                Log::warning('YooKassa webhook: order_id missing in metadata', $payload);
                return false;
            }

            $order = Order::find($orderId);
            if (!$order) {
                Log::warning('YooKassa webhook: order not found', ['order_id' => $orderId]);
                return false;
            }

            // Дополнительно сверяем payment_id
            if ($order->yookassa_payment_id && $order->yookassa_payment_id !== $paymentId) {
                Log::warning('YooKassa webhook: payment_id mismatch', [
                    'order_id' => $orderId,
                    'expected' => $order->yookassa_payment_id,
                    'got'      => $paymentId,
                ]);
                return false;
            }

            // Обновляем по типу события
            if ($notification instanceof NotificationSucceeded) {
 
                // Меняем статус и уменьшаем остатки в транзакции
                \DB::transaction(function () use ($order, $payload) {
                    $order->update([
                        'status'           => 'paid',
                        'yookassa_status'  => 'succeeded',
                        'yookassa_payload' => $payload,
                        'paid_at'          => now(),
                    ]);
 
                    // Уменьшаем stock у каждого товара в заказе
                    foreach ($order->items as $item) {
                        if ($item->product_id) {
                            \App\Models\Product::where('id', $item->product_id)
                                ->where('stock', '>=', $item->qty)
                                ->decrement('stock', $item->qty);
                        }
                    }
                });
 
                return true;
            }

            if ($notification instanceof NotificationCanceled) {
                $order->update([
                    'status'           => 'canceled',
                    'yookassa_status'  => 'canceled',
                    'yookassa_payload' => $payload,
                    'canceled_at'      => now(),
                ]);
                return true;
            }

            // payment.waiting_for_capture и прочие — просто фиксируем статус
            $order->update([
                'yookassa_status'  => $paymentResponse->getStatus(),
                'yookassa_payload' => $payload,
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::error('YooKassa webhook error', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
            return false;
        }
    }

    /**
     * Запросить статус платежа у ЮКассы.
     * Используется на странице возврата — на случай если webhook задержался.
     */
    public function fetchPaymentStatus(string $paymentId): ?string
    {
        try {
            $payment = $this->client->getPaymentInfo($paymentId);
            return $payment->getStatus();
        } catch (\Throwable $e) {
            Log::error('YooKassa fetchPaymentStatus failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
