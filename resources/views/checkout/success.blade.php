@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">

<div class="checkout-result checkout-result--success">
    <div class="container">

        <div class="checkout-result__inner">
            <div class="checkout-result__seal" aria-hidden="true">
                <svg viewBox="0 0 120 120" fill="none">
                    <circle cx="60" cy="60" r="56" stroke="currentColor" stroke-width="0.8" opacity="0.3"/>
                    <circle cx="60" cy="60" r="46" stroke="currentColor" stroke-width="1" opacity="0.55"/>
                    <circle cx="60" cy="60" r="34" stroke="currentColor" stroke-width="2"/>
                    <path d="M44 60 L55 71 L78 48" stroke="currentColor" stroke-width="3"
                          stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <text x="60" y="98" text-anchor="middle" font-family="JetBrains Mono"
                          font-size="6" letter-spacing="1.4" fill="currentColor" stroke="none">ОПЛАЧЕНО</text>
                </svg>
            </div>

            <span class="checkout-result__kicker">расплатились</span>
            <h1 class="checkout-result__title">
                Готово.<br>Дарина <em>разжигает огонь</em>.
            </h1>

            <p class="checkout-result__lede">
                Заказ <strong>{{ $order->number }}</strong> принят.<br>
                Сначала Дарина сварит, потом упакует — и отправит выбранной службой.
                На каждом шаге пришлём весточку на <strong>{{ $order->contact_email }}</strong>.
            </p>

            {{-- Сводка заказа --}}
            <div class="checkout-result__order">
                <div class="checkout-result__row">
                    <span>Заказ</span>
                    <strong>{{ $order->number }}</strong>
                </div>
                <div class="checkout-result__row">
                    <span>Сумма</span>
                    <strong>{{ $order->totalFormatted() }}</strong>
                </div>
                <div class="checkout-result__row">
                    <span>Доставка</span>
                    <strong>{{ $order->deliveryLabel() }}</strong>
                </div>
                <div class="checkout-result__row">
                    <span>Куда</span>
                    <strong>{{ $order->delivery_city }}, {{ $order->delivery_address }}</strong>
                </div>
                <div class="checkout-result__row">
                    <span>Статус</span>
                    <strong class="checkout-result__status">{{ $order->statusLabel() }}</strong>
                </div>
            </div>

            <div class="checkout-result__actions">
                @auth
                    <a href="{{ route('orders') }}" class="btn-primary">
                        <span>Мои заказы</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </a>
                @endauth
                <a href="{{ route('catalog') }}" class="btn-ghost">
                    В кладовую за новым
                </a>
            </div>

            <p class="checkout-result__hint">
                Если что-то не так — напиши Дарине: <a href="mailto:hello@gostinec.ru">hello@gostinec.ru</a>
            </p>
        </div>
    </div>
</div>

@endsection
