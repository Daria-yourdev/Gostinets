@extends('admin.layout')

@section('title', "Заказ {$order->number}")
@section('heading')
    Заказ <span class="admin-mono">{{ $order->number }}</span>
@endsection

@section('content')

<a href="{{ route('admin.orders.index') }}" class="admin-back">← к списку заказов</a>

<div class="admin-order">

    {{-- ЛЕВО: позиции + контакты + доставка --}}
    <div class="admin-order__main">

        {{-- Позиции --}}
        <section class="admin-block">
            <header class="admin-block__head">
                <h2>Что в заказе</h2>
                <span class="admin-block__hint">{{ $order->items->count() }} позиций · {{ $order->items->sum('qty') }} банок</span>
            </header>

            <div class="admin-order-items">
                @foreach($order->items as $item)
                    <article class="admin-order-item">
                        <div class="admin-order-item__visual">
                            <img src="{{ asset($item->product_image ?: 'media/catalog/catalog-card-1.png') }}"
                                 alt="{{ $item->product_name }}">
                        </div>
                        <div class="admin-order-item__info">
                            @if($item->product)
                                <a href="{{ route('admin.products.edit', $item->product) }}" class="admin-link">
                                    <strong>{{ $item->product_name }}</strong>
                                </a>
                            @else
                                <strong>{{ $item->product_name }}</strong>
                                <span class="admin-tag admin-tag--mute">товар удалён</span>
                            @endif
                            @if($item->product_subtitle)
                                <div class="admin-mute">{{ $item->product_subtitle }}</div>
                            @endif
                        </div>
                        <div class="admin-order-item__qty admin-mono">× {{ $item->qty }}</div>
                        <div class="admin-order-item__price admin-mono">{{ $item->priceFormatted() }}</div>
                        <div class="admin-order-item__sum admin-mono">
                            <strong>{{ $item->subtotalFormatted() }}</strong>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="admin-order-totals">
                <div class="admin-order-totals__row">
                    <span>За банки</span>
                    <strong class="admin-mono">{{ number_format($order->subtotal, 0, '.', ' ') }} ₽</strong>
                </div>
                <div class="admin-order-totals__row">
                    <span>Доставка</span>
                    <strong class="admin-mono">
                        @if($order->delivery_cost > 0)
                            {{ number_format($order->delivery_cost, 0, '.', ' ') }} ₽
                        @else
                            <em>бесплатно</em>
                        @endif
                    </strong>
                </div>
                @if($order->discount > 0)
                    <div class="admin-order-totals__row">
                        <span>Скидка</span>
                        <strong class="admin-mono">−{{ number_format($order->discount, 0, '.', ' ') }} ₽</strong>
                    </div>
                @endif
                <div class="admin-order-totals__row admin-order-totals__row--total">
                    <span>Итого</span>
                    <strong class="admin-mono">{{ number_format($order->total, 0, '.', ' ') }} ₽</strong>
                </div>
            </div>
        </section>

        {{-- Контакты --}}
        <section class="admin-block">
            <header class="admin-block__head"><h2>Покупатель</h2></header>
            <dl class="admin-dl">
                <div>
                    <dt>Имя</dt>
                    <dd>{{ $order->contact_name }}</dd>
                </div>
                <div>
                    <dt>Email</dt>
                    <dd>
                        <a href="mailto:{{ $order->contact_email }}" class="admin-link">{{ $order->contact_email }}</a>
                    </dd>
                </div>
                <div>
                    <dt>Телефон</dt>
                    <dd>
                        <a href="tel:{{ $order->contact_phone }}" class="admin-link">{{ $order->contact_phone }}</a>
                    </dd>
                </div>
                @if($order->user)
                    <div>
                        <dt>Аккаунт</dt>
                        <dd>
                            <a href="{{ route('admin.users.show', $order->user) }}" class="admin-link">
                                {{ $order->user->name }} — все заказы
                            </a>
                        </dd>
                    </div>
                @else
                    <div>
                        <dt>Аккаунт</dt>
                        <dd class="admin-mute">оформлял гостем</dd>
                    </div>
                @endif
            </dl>
        </section>

        {{-- Доставка --}}
        <section class="admin-block">
            <header class="admin-block__head"><h2>Доставка</h2></header>
            <dl class="admin-dl">
                <div>
                    <dt>Способ</dt>
                    <dd>{{ $order->deliveryLabel() }}</dd>
                </div>
                <div>
                    <dt>Город</dt>
                    <dd>{{ $order->delivery_city }}{{ $order->delivery_zip ? ", индекс {$order->delivery_zip}" : '' }}</dd>
                </div>
                <div>
                    <dt>Адрес</dt>
                    <dd>{{ $order->delivery_address }}</dd>
                </div>
                @if($order->delivery_note)
                    <div>
                        <dt>Комментарий</dt>
                        <dd class="admin-mute">{{ $order->delivery_note }}</dd>
                    </div>
                @endif
            </dl>
        </section>
    </div>

    {{-- ПРАВО: статус + оплата + история --}}
    <aside class="admin-order__side">

        {{-- Статус заказа --}}
        <section class="admin-block">
            <header class="admin-block__head"><h2>Статус</h2></header>

            <div class="admin-status-current admin-status admin-status--{{ $order->status }}">
                {{ $order->statusLabel() }}
            </div>

            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="admin-status-form">
                @csrf
                @method('PATCH')

                <label class="admin-field">
                    <span class="admin-field__label">Изменить статус</span>
                    <select name="status" class="admin-field__select">
                        @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                            <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <button type="submit" class="admin-btn admin-btn--primary">
                    Сохранить
                </button>
            </form>
        </section>

        {{-- Оплата --}}
        <section class="admin-block">
            <header class="admin-block__head"><h2>Оплата</h2></header>

            <dl class="admin-dl">
                <div>
                    <dt>Сумма</dt>
                    <dd class="admin-mono"><strong>{{ number_format($order->total, 0, '.', ' ') }} ₽</strong></dd>
                </div>
                <div>
                    <dt>Через</dt>
                    <dd>ЮКасса</dd>
                </div>
                @if($order->yookassa_payment_id)
                    <div>
                        <dt>ID платежа</dt>
                        <dd class="admin-mono admin-tiny">{{ $order->yookassa_payment_id }}</dd>
                    </div>
                @endif
                @if($order->yookassa_status)
                    <div>
                        <dt>Статус ЮКассы</dt>
                        <dd class="admin-mono">{{ $order->yookassa_status }}</dd>
                    </div>
                @endif
                @if($order->paid_at)
                    <div>
                        <dt>Оплачен</dt>
                        <dd>{{ $order->paid_at->format('d.m.Y H:i') }}</dd>
                    </div>
                @endif
                @if($order->canceled_at)
                    <div>
                        <dt>Отменён</dt>
                        <dd>{{ $order->canceled_at->format('d.m.Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        {{-- История --}}
        <section class="admin-block">
            <header class="admin-block__head"><h2>История</h2></header>
            <ul class="admin-timeline">
                <li>
                    <span class="admin-timeline__dot"></span>
                    <div>
                        <strong>Создан</strong>
                        <small class="admin-mute">{{ $order->created_at->format('d.m.Y в H:i') }}</small>
                    </div>
                </li>
                @if($order->paid_at)
                    <li>
                        <span class="admin-timeline__dot admin-timeline__dot--ok"></span>
                        <div>
                            <strong>Оплачен</strong>
                            <small class="admin-mute">{{ $order->paid_at->format('d.m.Y в H:i') }}</small>
                        </div>
                    </li>
                @endif
                @if($order->canceled_at)
                    <li>
                        <span class="admin-timeline__dot admin-timeline__dot--err"></span>
                        <div>
                            <strong>Отменён</strong>
                            <small class="admin-mute">{{ $order->canceled_at->format('d.m.Y в H:i') }}</small>
                        </div>
                    </li>
                @endif
                <li>
                    <span class="admin-timeline__dot admin-timeline__dot--mute"></span>
                    <div>
                        <strong>Последнее изменение</strong>
                        <small class="admin-mute">{{ $order->updated_at->format('d.m.Y в H:i') }}</small>
                    </div>
                </li>
            </ul>
        </section>
    </aside>
</div>

@endsection