<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CheckoutController — оформление заказа и оплата через ЮКассу.
 *
 * Поток:
 *   GET  /checkout            → форма (показывается если корзина не пуста)
 *   POST /checkout            → создание заказа + редирект на ЮКассу
 *   GET  /checkout/{order}/return → возврат пользователя из ЮКассы
 *   GET  /checkout/{order}/success → страница «успешно оплачено»
 *   GET  /checkout/{order}/fail    → страница «не сложилось»
 */
class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private YooKassaService $yookassa,
    ) {}

    /**
     * GET /checkout — форма оформления
     */
    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart')
                ->with('flash', 'Сначала положи что-нибудь в мешочек.');
        }

        $items    = $this->cart->items();
        $subtotal = $this->cart->subtotal();

        return view('checkout.index', [
            'items'           => $items,
            'subtotal'        => $subtotal,
            'deliveryMethods' => Order::DELIVERY_METHODS,
            'freeShipFrom'    => 3000,
            'user'            => auth()->user(),
        ]);
    }

    /**
     * POST /checkout — создать заказ и платёж
     */
    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart');
        }

        $data = $request->validate([
            'contact_name'     => ['required', 'string', 'min:2', 'max:100'],
            'contact_email'    => ['required', 'email', 'max:160'],
            'contact_phone'    => ['required', 'string', 'min:10', 'max:32'],
            'delivery_method'  => ['required', 'in:cdek,post,pickup'],
            'delivery_city'    => ['required', 'string', 'max:80'],
            'delivery_address' => ['required', 'string', 'max:200'],
            'delivery_zip'     => ['nullable', 'string', 'max:16'],
            'delivery_note'    => ['nullable', 'string', 'max:500'],
            'agree'            => ['accepted'],
        ], [
            'contact_name.required'     => 'Без имени гостинцу не уйти.',
            'contact_email.required'    => 'Куда слать весточку об отправке?',
            'contact_email.email'       => 'Это не похоже на почту.',
            'contact_phone.required'    => 'Курьеру понадобится связаться.',
            'delivery_city.required'    => 'Город не указан.',
            'delivery_address.required' => 'Куда нести гостинец?',
            'agree.accepted'            => 'Без согласия с правилами заказ не оформить.',
        ]);

        // Снапшот корзины и сумм
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $delivery = $this->cart->deliveryCost($data['delivery_method']);
        $total = $subtotal + $delivery;

        // Создаём заказ + позиции в транзакции
        $order = DB::transaction(function () use ($data, $items, $subtotal, $delivery, $total) {
            $order = Order::create(array_merge($data, [
                'user_id'       => auth()->id(),
                'subtotal'      => $subtotal,
                'delivery_cost' => $delivery,
                'discount'      => 0,
                'total'         => $total,
                'status'        => 'pending',
            ]));

            foreach ($items as $row) {
                $p = $row['product'];
                OrderItem::create([
                    'order_id'         => $order->id,
                    'product_id'       => $p->id,
                    'product_name'     => $p->name,
                    'product_subtitle' => $p->subtitle,
                    'product_image'    => $p->image_path,
                    'price'            => $p->price,
                    'qty'              => $row['qty'],
                    'subtotal'         => $row['subtotal'],
                ]);
            }

            return $order;
        });

        // Создаём платёж в ЮКассе
        $confirmationUrl = $this->yookassa->createPayment($order);

        if (!$confirmationUrl) {
            // Платёж создать не удалось — переводим заказ в canceled
            $order->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            return redirect()->route('checkout.fail', $order)
                ->with('flash', 'Не удалось связаться с банком. Попробуй чуть позже.');
        }

        // Корзину пока НЕ чистим — если пользователь откажется от оплаты,
        // он сможет вернуться. Очистка в success-обработчике.

        return redirect()->away($confirmationUrl);
    }

    /**
     * GET /checkout/{order}/return — возврат пользователя из ЮКассы
     * (это return_url, который мы передали при создании платежа)
     *
     * На этот момент webhook мог ещё не прийти, поэтому
     * проверяем статус платежа активно — спрашиваем у ЮКассы.
     */
    public function return(Order $order)
    {
        if ($order->yookassa_payment_id) {
            $status = $this->yookassa->fetchPaymentStatus($order->yookassa_payment_id);

            // Если оплата успешна — обновляем заказ (если webhook ещё не прилетел)
            if ($status === 'succeeded' && !$order->isPaid()) {
                $order->update([
                    'status'          => 'paid',
                    'yookassa_status' => 'succeeded',
                    'paid_at'         => now(),
                ]);
            }

            if ($status === 'canceled' && !$order->isCanceled()) {
                $order->update([
                    'status'          => 'canceled',
                    'yookassa_status' => 'canceled',
                    'canceled_at'     => now(),
                ]);
            }
        }

        // Решаем куда вести
        if ($order->isPaid()) {
            // Корзина чистится только после подтверждённой оплаты
            $this->cart->clear();
            return redirect()->route('checkout.success', $order);
        }

        if ($order->isCanceled()) {
            return redirect()->route('checkout.fail', $order);
        }

        // Платёж ещё в процессе — показываем «обрабатывается»
        return view('checkout.pending', compact('order'));
    }

    /**
     * GET /checkout/{order}/success — успешно оплачено
     */
    public function success(Order $order)
    {
        // Защита: показываем только владельцу или по сессионной метке
        $this->ensureCanView($order);

        return view('checkout.success', compact('order'));
    }

    /**
     * GET /checkout/{order}/fail — отмена платежа
     */
    public function fail(Order $order)
    {
        $this->ensureCanView($order);
        return view('checkout.fail', compact('order'));
    }

    /**
     * Проверка прав на просмотр заказа.
     * Свой заказ видит owner, а гость — недавний (по сессии).
     */
    private function ensureCanView(Order $order): void
    {
        // Авторизованный — должен быть владельцем
        if (auth()->check() && $order->user_id === auth()->id()) return;

        // Гость — должен иметь ID этого заказа в сессии
        $recent = session('recent_orders', []);
        if (in_array($order->id, $recent, true)) return;

        // Сохраняем в сессию при первом просмотре после оплаты
        $recent[] = $order->id;
        session(['recent_orders' => array_slice(array_unique($recent), -10)]);
    }
}
